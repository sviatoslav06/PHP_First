<?php global $pdo; ?>
<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Головна сторінка</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/site.css">
</head>
<body>

<?php include($_SERVER["DOCUMENT_ROOT"] . "/_header.php"); ?>

<main>
    <div class="container">
        <?php include($_SERVER["DOCUMENT_ROOT"] . "/config/connection_database.php"); ?>

        <h1 class="text-center">Список категорій</h1>
        <a href="/create.php" class="btn btn-success">Додати</a>
        <?php
        // Query to select data from a specific table
        $stmt = $pdo->query("SELECT id, name, description, image FROM categories");
        // Fetch data as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Check if there are rows in the result
        if (count($result) > 0) {
            ?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Фото</th>
                    <th scope="col">Назва</th>
                    <th scope="col">Опис</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($result as $row) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $row["id"] ?></th>
                        <td>
                            <img src="/img/<?php echo $row["image"] ?>" alt="Фото" width="100">
                        </td>
                        <td><?php echo $row["name"] ?></td>
                        <td><?php echo $row["description"] ?></td>
                        <td>
                            <a href="change.php?category_id=<?php echo $row["id"]; ?>" class="btn btn-info">Змінити</a>
                            <form action="delete.php" method="post">
                                <input type="hidden" name="category_id" value="<?php echo $row["id"]; ?>">
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                    Видалити
                                </button>
                            </form>
                        </td>
                    </tr>

                    <?php
                } ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<div class="alert alert-primary" role="alert">
                    Категорії відсутні
                  </div>';
        }
        ?>

    </div>
</main>


<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>