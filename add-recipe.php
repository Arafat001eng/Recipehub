<?php 
include('../conn/conn.php');

$recipeName = $_POST['recipe_name'];
$recipeCategory = $_POST['tbl_category_id'];
$recipeIngredients = $_POST['recipe_ingredients'];
$recipeProcedure = $_POST['recipe_procedure'];

// Recipe image
$recipeImageName = $_FILES['recipe_image']['name'];
$recipeImageTmpName = $_FILES['recipe_image']['tmp_name'];

$target_dir = "../uploads/";
// give unique name to avoid overwrite
$newFileName = time() . "_" . basename($recipeImageName);
$target_file = $target_dir . $newFileName;

$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if valid image
if ($recipeImageTmpName != "") {
    $check = getimagesize($recipeImageTmpName);
    if ($check === false) {
        $uploadOk = 0;
    }
}

// Allow max 5MB instead of 500KB
if ($_FILES["recipe_image"]["size"] > 5000000) {
    $uploadOk = 0;
}

// Allow only certain image formats
$allowed = ["jpg","jpeg","png","gif"];
if(!in_array($imageFileType, $allowed)) {
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    echo "<script>
    alert('Invalid image. Please upload JPG, PNG, or GIF under 5MB.');
    window.location.href = 'http://localhost/RecipeHub/index.php#food';
    </script>";
} else {
    if (move_uploaded_file($recipeImageTmpName, $target_file)) {
        $stmt = $conn->prepare("INSERT INTO `tbl_recipe` 
        (`tbl_category_id`, `recipe_image`, `recipe_name`, `recipe_ingredients`, `recipe_procedure`) 
        VALUES (:recipeCategory, :recipeImage, :recipeName, :recipeIngredients, :recipeProcedure)");

        $stmt->bindParam(':recipeCategory', $recipeCategory);
        $stmt->bindParam(':recipeImage', $newFileName);
        $stmt->bindParam(':recipeName', $recipeName);
        $stmt->bindParam(':recipeIngredients', $recipeIngredients);
        $stmt->bindParam(':recipeProcedure', $recipeProcedure);

        $stmt->execute();

        echo "<script>
        alert('Successfully Added'); 
        window.location.href = 'http://localhost/RecipeHub/index.php#food';
        </script>";
    } else {
        echo "<script>
        alert('Image upload failed.'); 
        window.location.href = 'http://localhost/RecipeHub/index.php#food';
        </script>";
    }
}
?>
