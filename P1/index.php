<?php
echo "WebServer ID: ";
echo gethostname();

# Configure connexion
$servidor = "Database";
$port = "5432";
$dbname = "AppDB";
$user = "useradmin";
$password = "secure1234";
$conn = pg_connect("host=$servidor port=$port dbname=$dbname user=$user password=$password");

# Check connexion
if (!$conn) {
  echo "An error occurred.\n";
  exit;
}

# Prepare insert query
$query = 'INSERT INTO "AppTable" ("WebServer", "Datetime") VALUES ($1, now())';
$params = [gethostname()];
# Run insert query - Fem ús de pg_query_params per seguretat injection-free
pg_query_params($conn, $query, $params);

# Run read query
$query = 'SELECT COUNT(*) FROM "AppTable" WHERE "WebServer"=$1';
$result = pg_query_params($conn,$query,$params);
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