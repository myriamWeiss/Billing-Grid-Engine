-- פינוי אם קיים
DROP TABLE IF EXISTS customer_groups, calls1, calls2;

-- חלוקת 50 הלקוחות ל-2 קבוצות לפי customer_id (25/25)
CREATE TABLE customer_groups engine=innodb AS
SELECT DISTINCT
       customer_id,
       NTILE(2) OVER (ORDER BY customer_id) AS grp
FROM calls;

-- יצירת שתי הטבלאות לפי המיפוי
CREATE TABLE calls1 engine=innodb AS
SELECT c.*
FROM calls c
JOIN customer_groups g USING (customer_id)
WHERE g.grp = 1;

CREATE TABLE calls2 engine=innodb AS
SELECT c.*
FROM calls c
JOIN customer_groups g USING (customer_id)
WHERE g.grp = 2;
