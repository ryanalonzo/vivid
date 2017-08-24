<?php

namespace Epoch\Controllers;

use Epoch\Models\Product;

class ProductController
{
    /**
     * Display all products
     */
    function showProducts()
    {
        $product = new Product;

        $products = $product->all();

        return view('products', ['products' => $products]);
    }
    /**
     * Add product into the database
     */
    function addProduct()
    {
        if(isset($_POST['add'])) {

            $product = new Product;

            $match = $product->where('prod_name', $_POST['prod_name'])
                  ->get();
            if($match) {
                echo "<script>alert('Product already exists');window.location = 'addNewProduct'</script>";
                exit;
            } else {
                $target_dir = "images/products/";
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check == false) {
                    echo "<script>alert('File is not an image.');window.location = 'addNewProduct'</script>";
                    $uploadOk = 0;
                }
                // Check if file already exists
                if (file_exists($target_file)) {
                    echo "<script>alert('Sorry, file already exists..');window.location = 'addNewProduct'</script>";
                    $uploadOk = 0;
                }
                // Check file size
                if ($_FILES["fileToUpload"]["size"] > 500000) {
                    echo "<script>alert('Sorry, your file is too large.');window.location = 'addNewProduct'</script>";
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');window.location = 'addNewProduct'</script>";
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "<script>alert('Sorry, your file was not uploaded.');window.location = 'addNewProduct'</script>";
                // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                        $image = basename( $_FILES["fileToUpload"]["name"]);

                        $input = [
                            'prod_name' => $_POST['prod_name'],
                            'unit_price'=> $_POST['unit_price'],
                            'stocks'    => $_POST['stocks'],
                            'image_src' => $image
                        ];

                        $product->create($input);
                        echo "<script>alert('Successfully added!.');window.location = 'products'</script>";
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                }
            }
        }
    }

    function editProduct()
    {
        $product = new Product;

        if(isset($_POST['edit'])) {
            $prodID = $_POST['prod_id'];

            $prodDetails = $product->where('id', $prodID)
                                   ->get();

            if(!isset($_SESSION['prodDetails'])) {
                $_SESSION['prod_details'] = [];
            }

            $_SESSION['prod_details'] = $prodDetails;

        }

        if(isset($_POST['update'])) {
            $product = new Product;

            $input = [
                'prod_name' => $_POST['prod_name'],
                'unit_price'=> $_POST['unit_price'],
                'stocks'    => $_POST['stocks']
            ];

            $product->update($input, $_POST['prod_id']);
            echo "<script>alert('Successfully Updated!.');window.location = 'products'</script>";
        }

        if(isset($_POST['delete'])) {
            $product = new Product;

            $prodID = $_POST['prod_id'];

            $match = $product->where('id', $prodID)
                             ->get();

            foreach($match as $prod) {
                $product->delete('products', $prodID);

                unlink("images/products/$prod->image_src");

                header('Location: products');
            }
        }
    }
}