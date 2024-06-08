<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logical Argument Validator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Logical Argument Validator</h1>
        <form method="post">
            <label for="expression">Enter Logical Expression:</label>
            <input type="text" id="expression" name="expression" required>
            <input type="submit" value="Generate Truth Table">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $expression = $_POST["expression"];
            generateTruthTable($expression);
        }

        function generateTruthTable($expression) {
            $variables = [];
            preg_match_all('/[a-zA-Z]/', $expression, $variables);
            $variables = array_unique($variables[0]);
            $numRows = pow(2, count($variables));
            $results = [];

            echo "<table border='1'>";
            echo "<tr>";
            foreach ($variables as $var) {
                echo "<th>{$var}</th>";
            }
            echo "<th>{$expression}</th>";
            echo "</tr>";

            for ($i = 0; $i < $numRows; $i++) {
                $values = [];
                foreach ($variables as $j => $var) {
                    $values[$var] = ($i >> (count($variables) - $j - 1)) & 1 ? 'T' : 'F';
                }
                echo "<tr>";
                foreach ($variables as $var) {
                    echo "<td>{$values[$var]}</td>";
                }
                $evaluated = evaluateExpression($expression, $values);
                echo "<td>{$evaluated}</td>";
                echo "</tr>";
                $results[] = $evaluated;
            }
            echo "</table>";

            // Check for tautology and validity
            checkTautologyAndValidity($results);
        }

        function evaluateExpression($expression, $values) {
            foreach ($values as $var => $val) {
                $expression = str_replace($var, $val, $expression);
            }
            // Replace logical symbols with PHP logical operators
            $expression = str_replace(['T', 'F', '∧', '∨', '¬', '→', '↔'], ['true', 'false', ' && ', ' || ', ' ! ', ' => ', ' == '], $expression);
            
            // Custom replacements for implications and double implications
            $expression = preg_replace('/([a-zA-Z]+)\s*=>\s*([a-zA-Z]+)/', '(!$1 || $2)', $expression);
            $expression = preg_replace('/([a-zA-Z]+)\s*==\s*([a-zA-Z]+)/', '(($1 && $2) || (!$1 && !$2))', $expression);

            $expression = 'return ' . $expression . ';';
            $evaluated = eval($expression) ? 'T' : 'F';
            return $evaluated;
        }

        function checkTautologyAndValidity($results) {
            $isTautology = true;
            foreach ($results as $result) {
                if ($result !== 'T') {
                    $isTautology = false;
                    break;
                }
            }

            echo "<h2>Results</h2>";
            if ($isTautology) {
                echo "<p>The logical expression is a tautology.</p>";
                echo "<p>The argument is valid.</p>";
            } else {
                echo "<p>The logical expression is not a tautology (contradiction).</p>";
                echo "<p>The argument is not valid (fallacy).</p>";
            }
        }
        ?>
    </div>
</body>
</html>
