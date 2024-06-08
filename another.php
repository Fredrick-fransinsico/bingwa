<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semantic Tableaux with Tree Visualization</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .tableau {
            margin-top: 20px;
            border-collapse: collapse;
        }
        .tableau th, .tableau td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        .closed {
            color: red;
        }
        .open {
            color: green;
        }
        .tree {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .node {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 20px;
        }
        .branch {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Semantic Tableaux with Tree Visualization</h1>
        <form method="post">
            <label for="expression">Enter Premises (Separated by commas):</label>
            <br>
            <textarea id="expression" name="expression" rows="4" cols="50" required></textarea>
            <br><br>
            <input type="submit" value="Construct Tableau">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $premises = explode(",", $_POST["expression"]);
            $tableau = new Tableau($premises);
            $tableau->constructTableau();
            $tableau->displayTableau();
            $tableau->displayTree();
        }

        class Tableau {
            private $premises;
            private $branches;

            public function __construct($premises) {
                $this->premises = $premises;
                $this->branches = [new Branch([])];
            }

            public function constructTableau() {
                foreach ($this->premises as $premise) {
                    $this->expandBranches($premise);
                }
            }

            private function expandBranches($expression) {
                foreach ($this->branches as $branch) {
                    $branch->expand($expression);
                }
            }

            public function displayTableau() {
                echo "<h2>Tableau</h2>";
                echo "<table class='tableau'>";
                echo "<tr><th>Branch</th><th>Status</th></tr>";
                foreach ($this->branches as $branch) {
                    echo "<tr>";
                    echo "<td>";
                    echo implode(", ", $branch->getExpressions());
                    echo "</td>";
                    echo "<td class='" . ($branch->isClosed() ? "closed" : "open") . "'>";
                    echo $branch->isClosed() ? "CLOSED" : "OPEN";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }

            public function displayTree() {
                echo "<h2>Tree Visualization</h2>";
                echo "<div class='tree'>";
                foreach ($this->branches as $branch) {
                    echo "<div class='node'>";
                    $this->displayBranchTree($branch);
                    echo "</div>";
                }
                echo "</div>";
            }

            private function displayBranchTree($branch) {
                echo "<div>";
                echo "<div class='branch'>";
                echo implode(", ", $branch->getExpressions());
                echo "</div>";
                if ($branch->isClosed() || $branch->isComplete()) {
                    echo "<div class='" . ($branch->isClosed() ? "closed" : "open") . "'>";
                    echo $branch->isClosed() ? "CLOSED" : "OPEN";
                    echo "</div>";
                } else {
                    foreach ($branch->getChildren() as $child) {
                        $this->displayBranchTree($child);
                    }
                }
                echo "</div>";
            }
        }

        class Branch {
            private $expressions;
            private $children;
            private $closed;

            public function __construct($expressions) {
                $this->expressions = $expressions;
                $this->children = [];
                $this->closed = false;
            }

            public function expand($expression) {
                $literal = $this->extractLiteral($expression);

                if ($literal !== null) {
                    if (in_array($literal, $this->expressions)) {
                        $this->closed = true;
                    } else {
                        $this->expressions[] = $literal;
                    }
                } else {
                    $alpha = $this->extractAlpha($expression);
                    $beta = $this->extractBeta($expression);

                    if ($alpha !== null) {
                        $child = new Branch($this->expressions);
                        $child->expand($alpha);
                        $this->children[] = $child;
                    } elseif ($beta !== null) {
                        $child1 = new Branch($this->expressions);
                        $child2 = new Branch($this->expressions);
                        $child1->expand($beta[0]);
                        $child2->expand($beta[1]);
                        $this->children[] = $child1;
                        $this->children[] = $child2;
                    }
                }
            }

            public function getExpressions() {
                return $this->expressions;
            }

            public function isClosed() {
                return $this->closed;
            }

            public function isComplete() {
                return empty($this->children);
            }

            public function getChildren() {
                return $this->children;
            }

            private function extractLiteral($expression) {
                $expression = trim($expression);
                if ($expression[0] === "~") {
                    return substr($expression, 1);
                } else {
                    return $expression;
                }
            }

            private function extractAlpha($expression) {
                if (strpos($expression, "(") === 0) {
                    $count = 0;
                    for ($i = 0; $i < strlen($expression); $i++) {
                        if ($expression[$i] === "(") {
                            $count++;
                        } elseif ($expression[$i] === ")") {
                            $count--;
                        }
                        if ($count === 0) {
                            return substr($expression, 1, $i - 1);
                        }
                    }
                }
                return null;
            }

            private function extractBeta($expression) {
                if (strpos($expression, "~(") === 0) {
                    $count = 0;
                    for ($i = 0; $i < strlen($expression); $i++) {
                        if ($expression[$i] === "(") {
                            $count++;
                        } elseif ($expression[$i] === ")") {
                            $count--;
                        }
                        if ($count === 0) {
                            $part1 = substr($expression, 2, $i - 2);
                            $part2 = substr($expression, $i + 1, strlen($expression) - $i - 1);
                            return [$part1, $part2];
                        }
                    }
                }
                return null;
            }
        }
        ?>
    </div>
</body>
</html>
