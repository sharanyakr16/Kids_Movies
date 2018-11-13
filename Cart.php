<?php include"includes/navbar.php"; ?>
<?php

// Class item represents a product that is in the shopping cart
class item{
    var $name;//name
    var $quantity;//quantity
    var $price;//price
    function item($name,$quantity,$price){//assigning the variables
        $this->name=$name;
        $this->quantity=$quantity;
        $this->price=$price;
    }
}

// Class cart represents a shopping cart with the variable $session_cart representing the list of items in the cart
class cart{
    // constructor
    function cart(){
        $this->sessionStart();
    }

    // Check for a previous session, start one if one isn't found and retrieve or generate shopping_cart
    function sessionStart(){
        global $session_cart;                   // global varriable - array of items in cart
        session_start();                        // start a session if one isn't found
        if(isset($_SESSION['session_cart'])){   // if a previouis session exists, get the data associated with the session_cart
            $session_cart=$_SESSION['session_cart'];
        }else{
            $session_cart=Array();              // if there's no session_cart, initialize one as an empty array
            $_SESSION['session_cart']=$session_cart;
        }
    }

    // Empty the cart
    function emptyCart(){
        session_unset();
        session_destroy();
        $this->cart();
    }

    // Register an item in the session and add it to the cart or add to an existing item quantity
    function registerOrAddItem($name, $quantity, $price){
        global $session_cart;
        if($session_cart==""){                       // start a session if one isn't found
            $this->sessionStart();
        }
        foreach($session_cart as $item){                // check if this product is already in cart, if so, update it
            if ($item->name==$name) {
                $q=$item->quantity+$quantity;
                $this->editItem($name,($q));
                $_SESSION['session_cart']=$session_cart;  // add the updated $session_cart array to the SESSION variable
                return true;
            }
        }
        $item=new item($name,$quantity,$price);   // if not in the cart, create item
        $session_cart[]=$item;                      // add the new item to the array $session_cart
        $_SESSION['session_cart']=$session_cart;  // add the updated $session_cart array to the SESSION variable
        return true;
    }

    // Update an item quantity by name
    function editItem($name,$quantity){
        global $session_cart;
        if($session_cart==""){                          // start a session if one isn't found
            $this->sessionStart();
            return false;
        }
        reset($session_cart);                               // reset pointer to the array first element
        foreach($session_cart as $item){                  // search the $session_cart array for the item to edit
            if($item->name==$name){                     // if a matching item is found, update it's quantity
                $item->quantity=$quantity;
                $_SESSION['session_cart']=$session_cart;  // add the updated $session_cart array to the SESSION variable
                return true;
            }
        }
        return false;                                       // if unable to find the item in the cart
    }

    //Delete an item from the $session_cart array by name
    function deleteItem($name){
        global $session_cart;
        if($session_cart == ""){                              // start a session if one isn't found
            $this->sessionStart();
            return false;
        }else{
            reset($session_cart);                               // set pointer in array to first element
            $i=0;
            foreach($session_cart as $item){
                if($item->name==$name){
                    array_splice($session_cart,$i,1);
                    if($session_cart==""){                         // start a session if one isn't found
                        $this->sessionStart();
                    }
                    $_SESSION['session_cart']=$session_cart;  // add the updated $session_cart array to the SESSION variable
                    return true;
                }
                $i++;
            }
            return false;
        }
    }

    //Remove one from the quantity of an item
    function removeOne($name,$quantity){
        global $session_cart;
        if($session_cart==""){                                  // start a session if one isn't found
            $this->sessionStart();
            return false;
        }else{
            foreach($session_cart as $item){            //else add the product details into the session cart
                if($item->name==$name){     //add the name details into the session cart
                    $this->editItem($name,($quantity-1));
                    if($item->quantity==0){
                        $this->deleteItem($name);
                    }
                    return true;
                }
            }
            return false;
        }
    }
}
?>
<!--creating the HTML view page-->

<html>
<body >

   

   <br><br>
<!--styling the page using bootstrap-->
   <div class="container">
    <div class="jumbotron">

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2 class="panel-title"><b>Shopping Cart</b></h2>
            </div>
            
            <div class="panel-body">   <!--creating a table for displaying the cart contents-->
                <table class="table" width="100%">
                        <tr>
                            <td colspan="6">
                                <table cellpadding="20px" width="100%">
                                 <?php
                                $cart=new cart(); //Create a new cart in php
                                $i=0;
                                $numItems=0;//number of items =0
                                $total=0;

                                if($_POST['Desired_Action']=="Adding"){         // add or increase quantity case
                                    $cart->registerOrAddItem($_POST['name'],$_POST['quantity'],$_POST['price']);
                                }
                                else if($_POST['Desired_Action']=="Deleting"){  // remove from cart case
                                    $cart->deleteItem($_POST['name']);
                                }
                                else if($_POST['Desired_Action']=="Editing"){   // increase quantity case
                                    $cart->editItem($_POST['name'],$_POST['quantity']);
                                }
                                else if($_POST['Desired_Action']=="Removing"){  // decrease quantity case
                                    $cart->removeOne($_POST['name'],$_POST['quantity']);
                                }
                                else if($_POST['Desired_Action']=="RemoveAll"){ // empty cart case
                                    $cart->emptyCart();
                                }
                                reset($session_cart);//resets the session cart

                                foreach($session_cart as $item){
                                    $numItems++;//adding items in the cart
                                }
                                if($numItems==0){//if no items in the cart, print the shopping cart is empty
                                    ?>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td colspan="5">
                                            <h2>It's empty!</h2>
                                        </td>
                                    </tr>
                                    <?php
                                }else{
                                    foreach($session_cart as $i=>$item){//else save the values of the product into variables
                                        $namefix=$item->name;
                                        $namefix=str_replace(" ", "", $namefix);
                                        $image="images/$namefix.png";
                                        ?>
                                        <tr style='border-bottom:black solid 2px'>
                                            <td>
                                                <?php $i ?>
                                            </td>
                                            <td>
                                                <!--img src='<?php echo $image ?>' alt='small image'-->
                                                <img src='<?php echo $image ?>' alt='small image'--><!--display the imageof the product-->
                                            </td>
                                            <td>
                                                <!--a href="product<?php echo $namefix ?>.html"><?php echo $item->name ?></a--><!--display the name of the product-->
                                                <a href="product<?php echo $i ?>.html"></a>
                                            </td>
                                            <td align='right'>
                                                $ <?php echo $item->price ?><!--display the price of the product-->
                                            </td>
                                            <td align='right'>
                                                X <?php echo $item->quantity ?> &ensp;&ensp;<!--display the quantity of the product-->
                                            </td>

                                            <td>
                                                <!--creating a form with method post to add values-->
                                                <form method='post'>
                                                    <input type='hidden' name='Desired_Action' value='Adding'><!--Add a hidden input type name in the form-->
                                                    <input type='hidden' name='name' value='<?php echo $item->name ?>'>
                                                    <input type='hidden' name='quantity' value='1'><!--passing a hidden input type quantity in the form-->
                                                    <input type='hidden' name='price' value='<?php echo $item->price ?>'><!--passing a hidden input type price in the form-->
                                                    <input type='image' src='images/arrowup.png' width="27px">
                                                </form>
                                                <!--creating a form with method post to remove values-->
                                                <form method='post'>
                                                    <input type='hidden' name='Desired_Action' value='Removing'/><!--Remove from the cart-->
                                                    <input type='hidden' name='name' value='<?php echo $item->name ?>'>
                                                    <input type='hidden' name='quantity' value='<?php echo $item->quantity ?>'>
                                                    <input type='image' src='images/arrowdown.png' width="27px">
                                                </form>
                                            </td>
                                            <td align='right' style='font-size:large;font-weight:bold'>
                                                $ <?php echo number_format(($item->price*$item->quantity),2) ?>
                                            </td>
                                            <td>
                                                <!--creating a form with method post to delete values-->
                                                <form method='post'>
                                                    <input type='hidden' name='Desired_Action' value='Deleting'>
                                                    <input type='hidden' name='name' value='<?php echo $item->name ?>'>&ensp;&ensp;&ensp;
                                                    <input type='image' src='images/delete.png' width="44px">
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                        $total=$total+($item->price*$item->quantity);//Calculating the total price
                                    }
                                }
                                ?>
                                <tr>
                                    <td colspan="6">
                                    </td>
                                    <td align="right">
                                        <h2>Total: $ <?php echo number_format($total,2) ?></h2><!--display the total value of the product-->
                                    </td>
                                </tr>
                            </table>
                            </td>
                    </tr>
                    </table>
                </div>
            </div>

            <br><br>

            <table cellpadding="20px" width="100%">
                <tr>
                        <td colspan="4">    <!--posting data to movieslist.php using form-->
                            <form method="post" action="movieslist.php">
                                <input type="submit" class="btn btn-primary btn-lg" style="width:250px;float:left;" value="Continue Shopping"><!--create a display button for continue shopping-->

                            </form>
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="Desired_Action" value="RemoveAll">
                                <input type="submit" class="btn btn-warning btn-lg" style="float:right;" value="Empty cart"><!--button to clear the items from the cart-->
                            </form>
                        </td>
                        <td>    <!--posting data to userinfo.php using form-->
                            <form method="post" action="userInfo.php">
                                <input type="submit" class="btn btn-success btn-lg" style="width:250px;float:right;" value="Checkout"><!--Submit button for checkout which passes info to userinfo.php-->
                            </form>
                        </td>
                    </tr>
                </table>
    </div>
</div><br><br>

</body>
</html>                        