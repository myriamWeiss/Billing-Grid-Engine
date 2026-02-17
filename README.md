# Billing-Grid-Engine
# Billing Engine Optimization & Distributed GRID Execution (MySQL + PHP)

A two-phase Cloud Data Engineering project:
1) **SQL performance optimization** for a monthly billing stored procedure processing **millions of call records**  
2) A **distributed GRID architecture** to enable **parallel execution** across multiple nodes (via localhost services)

---

## What this project does

The system generates a monthly **Billing** table based on input tables:

- `calls` (millions of rows)
- `customer`
- `price_plan`
- `discount`

Output:
- `billing` (monthly invoice per customer, including pricing + discounts)

Core idea:
- First, optimize the stored procedure to reduce heavy scans and expensive joins.
- Then, distribute the workload across GRID nodes and merge results back to the central DB.

---

## Phase 1 — SQL Performance Optimization

I optimized the monthly billing procedure (e.g., `GenerateMonthlyBills`) by applying:

- **Composite indexing** for the heavy `calls` workload
- **Early aggregation before JOINs** (reduce big data early, compute pricing/discounts on aggregated rows)
- **Temporary in-memory tables** to stage intermediate results
- **Upsert strategy** into `billing` to preserve historical months

This phase focuses on performance tuning for large-scale SQL execution on a single node.

---

## Phase 2 — Distributed GRID Architecture (Parallel Execution)

I extended the solution to run in parallel across GRID nodes.

High-level pipeline:
1. Split `calls` into balanced groups (by customer load)
2. Export input tables to JSON
3. Deploy/import into node databases
4. Run billing procedure on each node in parallel
5. Export node billing outputs and merge back into the central DB

> Note: This phase is designed for scalability and parallelization. Actual speedup depends on the environment/resources of the GRID nodes.

---


---

## How it works (key files)

### Orchestration
- `grid1/Team-1/gk_grid_process.txt`  
  A single process file that orchestrates the full pipeline:
  split → export → transfer → import → run billing → export results → merge

### SQL
- `grid1/Team-1/my_billing.sql`  
  Creates needed helper structures (temp tables/indexes if needed) and runs the billing procedure for a given month.

- `grid/Team-1/split_calls.sql`  
  Splits the original `calls` data into multiple subsets (example: `calls1`, `calls2`).

- `grid*/Team-1/create_db_to_grid.sql`  
  Creates the node database schema (e.g., `calls_billing1`, `calls_billing2`).

### Data movement (JSON-based)
- `export_table_to_json.php` — exports DB tables to JSON
- `get_file.php` — fetches JSON files across nodes
- `import_json_to_table.php` — imports JSON into DB tables







