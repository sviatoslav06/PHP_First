<?php
global $pdo;
include($_SERVER["DOCUMENT_ROOT"] . "/config/connection_database.php");

// Check if category ID is provided in the URL
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Fetch current category details from the database
    $sql = "SELECT name, image, description FROM categories WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $category_id);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the category exists
    if (!$category) {
        echo "Category not found.";
        exit;
    }

    // Assign fetched values to variables for pre-filling the form
    $name = $category['name'];
    $image = $category['image'];
    $description = $category['description'];
} else {
    echo "Category ID not provided.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST["name"];
        $description = $_POST["description"];

        // If a new image is uploaded, update the image file
        if (!empty($_FILES['image']['tmp_name'])) {
            $imageTmpName = $_FILES['image']['tmp_name'];
            $dir = "/img/";
            $image_name = uniqid() . ".jpg";

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg'];
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                throw new Exception('Invalid file type. Only JPEG files are allowed.');
            }

            $destination = $_SERVER["DOCUMENT_ROOT"] . $dir . $image_name;

            if (!move_uploaded_file($imageTmpName, $destination)) {
                throw new Exception('Failed to move uploaded file.');
            }

            // Update the image field in the database
            $updateImageSql = "UPDATE categories SET image = :image WHERE id = :id";
            $stmtUpdateImage = $pdo->prepare($updateImageSql);
            $stmtUpdateImage->bindParam(':id', $category_id);
            $stmtUpdateImage->bindParam(':image', $image_name);
            $stmtUpdateImage->execute();
        }

        // Update other fields in the database
        $updateSql = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
        $stmtUpdate = $pdo->prepare($updateSql);
        $stmtUpdate->bindParam(':id', $category_id);
        $stmtUpdate->bindParam(':name', $name);
        $stmtUpdate->bindParam(':description', $description);
        $stmtUpdate->execute();

        header("Location: /");
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- Rest of your HTML code remains unchanged -->

<!-- Modify the form to pre-fill fields with current values -->
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
        <h1 class="text-center">Змінити категорію</h1>
        <form class="offset-md-3 col-md-6" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <!-- Add a hidden input for category ID -->
            <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Назва</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
            </div>
            <div class="mb-3">
                <div id="photoContainer"></div>
                <label for="image" class="form-label">Фото</label>
                <input onchange="displayPhoto(this)" type="file" class="form-control" id="image" name="image">
                <img id="photo_preview" src="/img/<?php echo $image; ?>" alt="Category Photo" style="max-width: 200px;">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Опис</label>
                <textarea class="form-control" name="description" id="description"
                          rows="5"><?php echo $description; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Оновити</button>
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