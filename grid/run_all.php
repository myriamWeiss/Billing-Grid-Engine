<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Parallel Process Status</title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f9; }
  .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
  h1, h2 { color: #333; text-align: center; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
  th { background-color: #4CAF50; color: white; text-align:center; }
  tr:nth-child(even) { background-color: #f2f2f2; }
  .status-pending { color: #f39c12; font-weight: bold; }
  .status-fulfilled { color: #27ae60; font-weight: bold; }
  .status-rejected { color: #e74c3c; font-weight: bold; }
  .timer-box { text-align: center; margin-top: 20px; padding: 10px; border: 1px solid orange; border-radius: 10px; font-size: 1.2em; color:#000; }
</style>
</head>

<body>
<div class="container">
  <h1>GRID Computing Platform</h1>
  <h2>Parallel Processing Dashboard</h2>
  
  <div class="timer-box">
    <strong>Total Execution Time:</strong> <span id="total-time">processing...</span>
  </div>

  <table>
    <thead>
      <tr>
        <th>URL</th>
        <th>Status</th>
        <th>Execution Time (ms)</th>
        <th>Result Data</th>
      </tr>
    </thead>
    <tbody id="results-table-body"></tbody>
  </table>
  <p style="text-align: center; margin-top:50px;">All rights revered to Afeka Gideon Koch 2025</p>
</div>

<script>
// A list of URLs to fetch
// Load it from an exteranl file
const urls = [
  '../grid1/team-1/test.php',
  '../grid1/team-1/test.php',
  '../grid1/team-1/test.php',
  '../grid1/team-1/test.php',
  '../grid1/team-1/test.php',
  '../grid1/team-1/test2.php'
];

/**
 * Fetches multiple URLs and updates the UI with their status.
 * @param {string[]} urls An array of URLs to fetch.
 */
async function fetchAllSettledAndUpdateUI(urls) {
  const startTime = performance.now();
  const resultsBody = document.getElementById('results-table-body');
  const totalTimeElement = document.getElementById('total-time');

  // Initialize the table rows with pending status
  urls.forEach((url, index) => {
    const row = resultsBody.insertRow();
    row.id = `row-${index}`;
    row.innerHTML = `
      <td>${url}</td>
      <td class="status-pending">Pending...</td>
      <td>...</td>
      <td>...</td>
    `;
  });

  // Map each URL to a fetch promise
  const promises = urls.map(url => {
    const uniqueUrl = `${url}?cache=${Math.random()}`;
    return fetch(uniqueUrl)
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => ({ status: 'fulfilled', data }))
      .catch(error => ({ status: 'rejected', reason: error.message }));
  });

  // Process results as they come in and update the UI
  const updatePromises = promises.map((promise, index) => {
    const startTaskTime = performance.now();
    return promise.then(result => {
      const endTaskTime = performance.now();
      const taskDuration = (endTaskTime - startTaskTime).toFixed(2);
      const row = document.getElementById(`row-${index}`);
      
      let statusText = '';
      let resultData = '';

      if (result.status === 'fulfilled') {
        statusText = 'Completed';
        row.cells[1].className = 'status-fulfilled';
        resultData = JSON.stringify(result.data, null, 2);
      } else {
        statusText = 'Failed';
        row.cells[1].className = 'status-rejected';
        resultData = result.reason;
      }
      
      row.cells[1].textContent = statusText;
      row.cells[2].textContent = taskDuration;
      row.cells[3].textContent = resultData;
    });
  });

  // Wait for all updates to complete
  await Promise.all(updatePromises);

  // Calculate and display total execution time
  const endTime = performance.now();
  const duration = endTime - startTime;
  totalTimeElement.textContent = `${duration.toFixed(2)} ms`;
//  totalTimeElement.style.color = "#27ae60";
}

// Call the function with the list of URLs
fetchAllSettledAndUpdateUI(urls);
</script>

</body>
</html>