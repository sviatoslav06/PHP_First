<?php
global $pdo;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $imageTmpName = $_FILES['image']['tmp_name']; //Темпова назва файлу, коли він передаєтсья на сервер
    $dir = "/img/";
    $image_name = uniqid() . ".jpg";
//шлях для збереження файлу
    $destination = $_SERVER["DOCUMENT_ROOT"] . $dir . $image_name;
// Move the uploaded file to the destination
    move_uploaded_file($imageTmpName, $destination);
    include($_SERVER["DOCUMENT_ROOT"] . "/config/connection_database.php");
// SQL query to insert data
    $sql = "INSERT INTO categories (name, image, description) VALUES (:name, :image, :description)";
// Prepare the SQL statement
    $stmt = $pdo->prepare($sql);
// Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':image', $image_name);
    $stmt->bindParam(':description', $description);

// Execute the statement
    $stmt->execute();
    header("Location: /");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Головна сторінка</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/site.css">
</head>
<body>

<?php include($_SERVER["DOCUMENT_ROOT"] . "/_header.php") ?>

<main>
    <div class="container">
        <h1 class="text-center">Додати категорію</h1>
        <form class="offset-md-3 col-md-6" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="name" class="form-label">Назва</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <div id="photoContainer"></div>
                <label for="image" class="form-label">Фото</label>
                <input onchange="displayPhoto(this)" type="file" class="form-control" id="image" name="image" required>
                <img id="photo_preview" src="#" alt="Category Photo" onclick="openFileInput('image')"
                     style="max-width: 200px; display: none;">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Опис</label>
                <textarea class="form-control" name="description" id="description" rows="5"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Додати</button>
        </form>
    </div>
</main>

<script src="./js/bootstrap.bundle.min.js"></script>
<script>
    function displayPhoto(input) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('photo_preview').src = e.target.result;
            document.getElementById('photo_preview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }

    function openFileInput(inputId) {
        document.getElementById(inputId).click();
    }

    function validateForm() {
        var categoryName = document.getElementById('name').value;
        var photoPreview = document.getElementById('photo_preview').src;

        if (categoryName === "" || photoPreview === "about:blank") {
            alert("Please enter category name and select a photo.");
            return false;
        }

        return true;
    }
</script>
</body>
</html>
