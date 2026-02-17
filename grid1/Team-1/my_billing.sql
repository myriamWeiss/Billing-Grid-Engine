
create index ix_calls_date_cust_type_dur
 on calls (customer_id, call_type, duration, date);

/* price_plan: join with  (pp_id, call_type) */
CREATE INDEX ix_price_plan_ppid_type
  ON price_plan (pp_id, call_type);

/* discount: help range join with d_id  */
CREATE INDEX ix_discount_did_range
  ON discount (d_id, fromd, tilld);

/* customer:  c_id and price_plan_id */
CREATE INDEX ix_customer_id_plan
  ON customer (c_id, price_plan_id);

/* billing:  upsert */
ALTER TABLE billing
  ADD UNIQUE KEY uq_billing_customer_month (b_c_id, yearmonth);

ALTER TABLE calls
PARTITION BY RANGE (TO_DAYS(`date`)) (
  PARTITION pmin       VALUES LESS THAN (TO_DAYS('2025-08-01')),
  PARTITION p2025_08   VALUES LESS THAN (TO_DAYS('2025-09-01')),
  PARTITION p2025_09   VALUES LESS THAN (TO_DAYS('2025-10-01')),
  PARTITION pmax       VALUES LESS THAN MAXVALUE
);




DROP PROCEDURE IF EXISTS GenerateMonthlyBills;
DELIMITER $$

CREATE PROCEDURE GenerateMonthlyBills(IN p_year INT, IN p_month INT)
BEGIN
  DECLARE v_start DATE;
  DECLARE v_end   DATE;
  DECLARE v_year_month VARCHAR(7);

  SET v_start = STR_TO_DATE(CONCAT(p_year,'-',LPAD(p_month,2,'0'),'-01'), '%Y-%m-%d');
  SET v_end   = DATE_ADD(v_start, INTERVAL 1 MONTH);
  SET v_year_month = DATE_FORMAT(v_start, '%Y-%m');

  /* clean */
  DROP TEMPORARY TABLE IF EXISTS tmp_plan_map, tmp_nodisc, tmp_disc_bucket,
                               tmp_nodisc_usage, tmp_disc_usage, tmp_per_customer;

  /* for discount == 0 */
  CREATE TEMPORARY TABLE tmp_plan_map (
    customer_id   INT UNSIGNED,
    call_type     INT UNSIGNED,
    duration_cost INT,
    basic_fee     INT,
    discount_id   INT,
    monthly_fee   INT,
    sub_discount  DECIMAL(5,2),
    PRIMARY KEY (customer_id, call_type)
  ) ENGINE=Memory;

  INSERT INTO tmp_plan_map
  SELECT c.c_id, pp.call_type, pp.duration_cost, pp.basic_fee, pp.discount_id, pp.monthly_fee, c.sub_discount
  FROM customer c
  JOIN price_plan pp ON pp.pp_id = c.price_plan_id;


  CREATE TEMPORARY TABLE tmp_nodisc (
    customer_id INT UNSIGNED,
    call_type   INT UNSIGNED,
    pulses_sum  BIGINT,
    basic_fee   INT,
    PRIMARY KEY (customer_id, call_type)
  ) ENGINE=Memory;

  INSERT INTO tmp_nodisc
  SELECT
    ca.customer_id,
    ca.call_type,
    SUM( (ca.duration + pm.duration_cost - 1) DIV pm.duration_cost ) AS pulses_sum,
    MAX(pm.basic_fee) AS basic_fee
  FROM calls ca
  JOIN tmp_plan_map pm
    ON pm.customer_id = ca.customer_id
   AND pm.call_type   = ca.call_type
  WHERE ca.`date` >= v_start AND ca.`date` < v_end
    AND pm.discount_id = 0
  GROUP BY ca.customer_id, ca.call_type;

  /* discount != 0 */
  CREATE TEMPORARY TABLE tmp_disc_bucket (
    customer_id INT UNSIGNED,
    call_type   INT UNSIGNED,
    d_id        INT,
    fromd       INT,
    tilld       INT,
    percentage  DECIMAL(5,2),
    pulses_sum  BIGINT,
    basic_fee   INT,
    PRIMARY KEY (customer_id, call_type, d_id, fromd, tilld)
  ) ENGINE=Memory;

  INSERT INTO tmp_disc_bucket
  SELECT
    ca.customer_id,
    ca.call_type,
    d.d_id,
    d.fromd,
    d.tilld,
    d.percentage,
    SUM( (ca.duration + pm.duration_cost - 1) DIV pm.duration_cost ) AS pulses_sum,
    MAX(pm.basic_fee) AS basic_fee
  FROM calls ca
  JOIN tmp_plan_map pm
    ON pm.customer_id = ca.customer_id
   AND pm.call_type   = ca.call_type
  JOIN discount d
    ON d.d_id = pm.discount_id
   AND ca.duration >= d.fromd
   AND (d.tilld = 0 OR ca.duration <= d.tilld)
  WHERE ca.`date` >= v_start AND ca.`date` < v_end
    AND pm.discount_id <> 0
  GROUP BY ca.customer_id, ca.call_type, d.d_id, d.fromd, d.tilld, d.percentage;


  CREATE TEMPORARY TABLE tmp_nodisc_usage (
    customer_id INT UNSIGNED PRIMARY KEY,
    usage_total_nodisc DECIMAL(18,4)
  ) ENGINE=Memory;

  INSERT INTO tmp_nodisc_usage
  SELECT customer_id, SUM(pulses_sum * basic_fee)
  FROM tmp_nodisc
  GROUP BY customer_id;


  CREATE TEMPORARY TABLE tmp_disc_usage (
    customer_id INT UNSIGNED PRIMARY KEY,
    usage_total_disc DECIMAL(18,4)
  ) ENGINE=Memory;

  INSERT INTO tmp_disc_usage
  SELECT customer_id, SUM(pulses_sum * basic_fee * (1 - percentage/100.0))
  FROM tmp_disc_bucket
  GROUP BY customer_id;


  CREATE TEMPORARY TABLE tmp_per_customer (
    customer_id INT UNSIGNED PRIMARY KEY,
    usage_total DECIMAL(18,4),
    monthly_fee DECIMAL(18,4),
    sub_discount DECIMAL(5,2)
  ) ENGINE=Memory;

  INSERT INTO tmp_per_customer
  SELECT
    pm.customer_id,
    COALESCE(nd.usage_total_nodisc, 0) + COALESCE(ds.usage_total_disc, 0) AS usage_total,
    MAX(pm.monthly_fee)  AS monthly_fee,
    MAX(pm.sub_discount) AS sub_discount
  FROM tmp_plan_map pm
  LEFT JOIN tmp_nodisc_usage nd ON nd.customer_id = pm.customer_id
  LEFT JOIN tmp_disc_usage  ds ON ds.customer_id = pm.customer_id
  GROUP BY pm.customer_id;


  INSERT INTO billing (b_c_id, yearmonth, total, discount, to_pay)
  SELECT
    pc.customer_id AS b_c_id,
    v_year_month   AS yearmonth,
    (pc.usage_total + pc.monthly_fee)                               AS total,
    (pc.usage_total + pc.monthly_fee) * (pc.sub_discount/100.0)     AS discount,
    (pc.usage_total + pc.monthly_fee) * (1 - pc.sub_discount/100.0) AS to_pay
  FROM tmp_per_customer pc
  ON DUPLICATE KEY UPDATE
    total    = VALUES(total),
    discount = VALUES(discount),
    to_pay   = VALUES(to_pay);
  DROP TEMPORARY TABLE IF EXISTS tmp_plan_map, tmp_nodisc, tmp_disc_bucket,
                               tmp_nodisc_usage, tmp_disc_usage, tmp_per_customer;
END$$

DELIMITER ;

call GenerateMonthlyBills('2025', '08')

