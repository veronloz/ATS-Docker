<?php
echo "WebServer ID: ";
echo gethostname();

# Configure connexion
$conn = pg_connect(...);

# Check connexion
if (!$conn) {
  echo "An error occurred.\n";
  exit;
}

# Prepare insert query
$query = "...";

# Run insert query
pg_query(...);

# Run read query
$result = pg_query(...);
if (!$result) {
  echo "An error occurred.\n";
  exit;
}

# Show results
while ($row = pg_fetch_row($result)) {
  echo " - Num served requests: $row[0]";
  echo "<br />\n";
}

?>