<?php include("App_Core/App_Core.php");

if(!isset($_COOKIE['OfficeEarth_Cart']))
{
    $ID = md5(time().rand());
    setcookie('OfficeEarth_Cart',$ID,time()+3600*24*30, '/');
}



if(isset($_GET['Restart']) && $_GET['Restart'] == 'True')
{
    RestartSignup('True');
}

if(isset($_SESSION['CheckoutStep']))
{
    header("Location: /Signup/SpecialOffer");
    die();
}

if(isset($_GET['Checkout']) && $_GET['Checkout'] == 'True')
{
    $_SESSION['CheckoutStep'] = '1';
    header("Location: /Signup/SpecialOffer");
    die();
}

if(isset($_GET['Remove']))
{
    $Query = "DELETE FROM shopping_cart WHERE ShoppingCartID = '".mysql_real_escape_string($_GET['Remove'])."' AND CookieID = '".$_SESSION['OfficeEarth_Cart']."'";
    mysql_query($Query);
    
    header("Location: /Cart");
    die();
}

if(isset($_POST['CheckPromo']))
{
    if($_POST['Promo'] == 'OETest132')
    {
        $_SESSION['TestSettings'] = 'test';
        
        $_SESSION['CreditCard'] = '5105105105105100';
        $_SESSION['CCV'] = '666';
        $_SESSION['CCType'] = 'MASTERCARD';
        
        $_SESSION['PromoDiscount'] = '0';
        $_SESSION['Promo'] = 'OE Test Details';
        
        $PromoStatus = 'Your now using test settings';
    }else{
            
        $GetPromoQuery = "SELECT * FROM promo_tbl WHERE Promo_Code = '".$_POST['Promo']."' AND Promo_Valid = 'Y'";
        $GetPromo = mysql_query($GetPromoQuery);
        $GetPromo_row = mysql_fetch_assoc($GetPromo);
        if($GetPromo_row['Promo_Code'] == $_POST['Promo']){
            
        if(date('Y-m-d') > $GetPromo_row['Promo_ValidTo'])
        {
            mysql_query("UPDATE promo_tbl SET Promo_Valid = 'N' WHERE Promo_Code = '".$_POST['Promo']."'");
        }
            else
        {
        
        $Used = $GetPromo_row['Promo_Used'] + 1;
        
        mysql_query("UPDATE promo_tbl SET Promo_Used = '".$Used."' WHERE Promo_Code = '".$_POST['Promo']."'");
        
        if($Used >= $GetPromo_row['Promo_Max']){
        mysql_query("UPDATE promo_tbl SET Promo_Valid = 'N' WHERE Promo_Code = '".$_POST['Promo']."'");
        }
        if($GetPromo_row['Promo_Action'] == 'DollarSignup'){
            $PromoStatus = 'Your 39 dollar signup deal has been applied.' ;
        }else{
            if($GetPromo_row['Promo_Month'] == '1'){
                $PromoStatus = 'Your 1 Month Discount has been applied.' ;
            }else{
                $PromoStatus = 'Your $'.$GetPromo_row['Promo_Discount']. ' Discount has been applied.' ;
            }
        }
        $_SESSION['PromoDiscount'] = $GetPromo_row['Promo_Discount'];
        $_SESSION['PromoAction'] = $GetPromo_row['Promo_Action'];
        $_SESSION['Promo'] = $GetPromo_row['Promo_Code'];
    }
    }
    else
    {
        $PromoStatus = $_POST['Promo']. ' is an invalid Promo Code';  
    }
    }
}

if(isset($_GET['Add']))
{
    $Query = "INSERT INTO shopping_cart (
    `CookieID`,
    `PackageID`,
    `Date`
    )VALUES(
    '".$_SESSION['OfficeEarth_Cart']."',
    '".mysql_real_escape_string($_GET['Add'])."',
    '".date('Y-m-d H:i:s')."'
    );";
    mysql_query($Query);
    
    $Query = mysql_query("SELECT * FROM shopping_cart WHERE CookieID = '".$_SESSION['OfficeEarth_Cart']."' AND (PackageID = '13' OR PackageID = '14')");
    $Active = mysql_num_rows($Query);
    
    if($Active == 0)
    {
    
    $Query = mysql_query("SELECT * FROM shopping_cart WHERE CookieID = '".$_SESSION['OfficeEarth_Cart']."' AND PackageID = '".mysql_real_escape_string($_GET['Add'])."'");
    
    $Count = mysql_num_rows($Query);
    
    if($_GET['Add'] == '3')
    {
        $Query = mysql_query("SELECT * FROM shopping_cart WHERE CookieID = '".$_SESSION['OfficeEarth_Cart']."' AND (PackageID = '8' OR PackageID = '9')");
        $Rows = mysql_num_rows($Query);
        
        
        if($Rows == $Count)
        {
            if($_GET['Add'] == '8')
            {
                $PackageID = '13';
            }else{
                $PackageID = '14'; 
            }
                
            $Query = "INSERT INTO shopping_cart (
            `CookieID`,
            `PackageID`,
            `Date`
            )VALUES(
            '".$_SESSION['OfficeEarth_Cart']."',
            '".$PackageID."',
            '".date('Y-m-d H:i:s')."'
            );";
            mysql_query($Query);
        }
    }
    
    if($_GET['Add'] == '8' || $_GET['Add'] == '9')
    {
        $Query = mysql_query("SELECT * FROM shopping_cart WHERE CookieID = '".$_SESSION['OfficeEarth_Cart']."' AND PackageID = '3'");
        $Rows = mysql_num_rows($Query);
        
        
        if($Rows == $Count)
        {
            if($_GET['Add'] == '8')
            {
                $PackageID = '13';
            }else{
                $PackageID = '14'; 
            }
                
            $Query = "INSERT INTO shopping_cart (
            `CookieID`,
            `PackageID`,
            `Date`
            )VALUES(
            '".$_SESSION['OfficeEarth_Cart']."',
            '".$PackageID."',
            '".date('Y-m-d H:i:s')."'
            );";
            mysql_query($Query);
        }
    }
    }  
    header("Location: /Cart");
    die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo getSetting('ShortCompanyName', $APIToken);?> Signup</title>
<link rel="stylesheet" type="text/css" href="<?php echo getSetting('URL', $APIToken);?>App_Theme/<?php echo getSetting('Theme', $APIToken);?>/css/stylesheet.css" />

<meta name="description" content="Signup today, stop worrying tomorrow" />

<meta name="robots" content="index,follow" />

<link rel="shortcut icon" href="/favicon.ico" />

<?php echo getSetting('Head', $APIToken);?> 

<script type="text/javascript" src="<?php echo getSetting('URL', $APIToken);?>App_Theme/js/jquery-1.7.1.min.js"></script>

<script src="<?php echo getSetting('URL', $APIToken);?>App_Theme/js/jquery.jcorners.js" type="text/javascript"></script>

<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php echo getSetting('GoogleAnalytics', $APIToken);?>']);
	_gaq.push(['_trackPageview']);
	_gaq.push(['_trackPageLoadTime']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>

<script type="application/javascript">
function getPromo() 
{
	PromoCode = SignUp.PromoCode.value;
	
	$.post('App_Includes/promo.php', { PromoCode: PromoCode },
		function(output){
			$('#Promo').html(output);
		});
}
</script>
<link rel="stylesheet" type="text/css" href="<?php echo getSetting('URL', $APIToken);?>App_Theme/<?php echo getSetting('Theme', $APIToken);?>/css/custom.css" />
<style>
.AddResourceA {
	width:auto;
	height:30px;
	padding-left:20px;
	padding-right:20px;
	background:#093;
	border:1px solid #063;
	border-radius:2px;
	color:#FFF;
	text-height:30px;
	font-weight:bold;
	text-decoration:none;
	line-height:30px;
}
.AddResourceA:hover {
	box-shadow: 0px 0px 10px #0C3;
	color:#FFF;
}
</style>
</head>

<body>

<?php include('App_Includes/PhoneChat.php');?>

<div class="bodywrap">
  <div class="container"> 
    <div class="header">
      <div class="logo"></div>
      <?php include("App_Includes/Menu.php");?>
    </div>
    <div class="middle"> 
      <div class="wrapper">
        <div class="content"> 
         <div class="text">
         
         <div class="white-box-full LocationSearchShadow" style="margin-bottom:10px;">
              <div class="white-box-top" style="background:none; height:30px; border-top:1px #DEDEDE solid;"></div>
              <div class="vertical-box" style="padding-top:0px; width:650px;">
         
         	<h1>My Shopping List</h1>
         	
         	<?php
         	
         	$Query = "SELECT
         	shopping_cart.ShoppingCartID,
            package.Title,
            count(ShoppingCartID) as total,
            pricing.Price,
            location.PricePerClient,
            location.LocationID,
            location.`Name`
            FROM
            shopping_cart
            INNER JOIN package ON shopping_cart.PackageID = package.PackageID
            INNER JOIN pricing ON shopping_cart.PackageID = pricing.ItemID
            LEFT OUTER JOIN location ON shopping_cart.LocationID = location.LocationID
            WHERE
            pricing.Country = '".CountryCode."'
            AND pricing.Type = 'Package'
            AND shopping_cart.CookieID = '".$_SESSION['OfficeEarth_Cart']."'
            GROUP BY shopping_cart.PackageID, shopping_cart.LocationID";
            
            $SQL = mysql_query($Query);
            $Active = mysql_num_rows($SQL);
			$Total = 0;
			$mod = 0;
         	if($Active == 0){
         	    echo 'No items in your cart, <a href="/Pricing">go shopping</a>';
            }else{
         	?>
         	<table width="100%" border="0" cellpadding="5" cellspacing="0">
              <tr style="color:#fff;">
                <td height="45" bgcolor="#272727">Package</td>
                <td bgcolor="#272727">Quantity</td>
                <td bgcolor="#272727">Cost</td>
                <td bgcolor="#272727">&nbsp;</td>
              </tr>
              <?php while($Cart = mysql_fetch_object($SQL)){
                  if(isset($Cart->PricePerClient))
                {
                    $Total += Convert(getLocationCost($Cart->LocationID, CurrencyCode) * $Cart->total);
                }else{
                    $Total += Convert($Cart->Price * $Cart->total);
                }
				  
				  $mod++;
				  $color = ($mod % 2 == 0) ? "ffffff" : "f3f7fb";
				?>
              <tr height="30px" style="line-height:30px; border-bottom:1px solid #999; background:#<?php echo $color;?>;">
                <td><?php if(isset($Cart->Name)){echo $Cart->Name;}else{echo $Cart->Title;}?></td>
                <td><?php echo $Cart->total;?></td>
                <?php 
                if(isset($Cart->PricePerClient))
                {
                    echo "<td>".CurrencyCode.' '.Convert(getLocationCost($Cart->LocationID, CurrencyCode) * $Cart->total)."</td>";
                }else{
                   echo "<td>".CurrencyCode.' '.Convert($Cart->Price * $Cart->total)."</td>"; 
                }
                
                ?>
                 
                <td><a href="Cart?Remove=<?php echo $Cart->ShoppingCartID ?>">Remove</a></td>
              </tr>
              <?php }if(isset($_SESSION['PromoDiscount'])){
                  $Total = $Total - $_SESSION['PromoDiscount'];
              ?>
              <tr height="30px" style="line-height:30px; border-bottom:1px solid #999;">
                <td>Promo Code Discount(Once-off Discount) - <?php echo CurrencyCode.' '.$_SESSION['PromoDiscount'];?></td>
                <td></td>
                <td>- $<?php echo $_SESSION['PromoDiscount'];?></td> 
                <td></td>
              </tr>
              <?php } ?>
              <tr height="30px" style="line-height:30px;">
                <td></td>
                <td align="right"><strong><font style="font-size:16px; color:#060;">Total</font></strong></td>
                <td><strong><font style="font-size:16px; color:#060;"><?php echo CurrencyCode.' '.number_format($Total,2);?></font></strong></td> 
                <td></td>
              </tr>
            </table>
            <div style="clear:both; height:30px;"></div>
            <a href="/Pricing">Add another package</a>
            <div style="clear:both; height:30px;"></div>
			<div style="padding:10px; background:#F2F2F2; border:#EEE solid 1px; border-radius:3px;">
                
                <div style="float:left; width:300px; border:#ccc solid 1px; border-radius:3px; padding:5px;">
                <h3 style="margin-bottom:10px;">Promo Code</h3>
                <?php if(isset($_SESSION['PromoDiscount'])){
					echo'<form name="CheckPromo" method="post">
                    <input type="text" name="Promo" class="SignUpTextboxDisabled" disabled="disabled" maxlength="9" value="'.$_SESSION['Promo'].'" style="float:left; width:297px;">';
					if(isset($PromoStatus)){echo $PromoStatus;}
               		echo '</form>';
				}else{
					echo'<form name="CheckPromo" method="post">
                    <input type="text" name="Promo" class="SignUpTextbox" maxlength="9" style="float:left; width:213px;">
                    <input type="submit" name="CheckPromo" class="AddResourceA" style="height:34px; border-radius:5px;" value="Check"/>
                    ';
					if(isset($PromoStatus)){echo $PromoStatus;}
               		echo '</form>';	
				}?>
                
                </div>
                <div style="float:left; width:315px; margin-top: 20px;">
                	<a href="/Cart?Checkout=True" class="AddResourceA" style="float:right;">Checkout</a>
                </div>
                <div style="clear: both;"></div>
            </div>
           <?php } ?>
           </div>
           <div class="white-box-bot" style="background:none; height:0px; border-bottom:1px #DEDEDE solid;"></div>
          </div>   
         </div>
        </div>
      </div>
      <div class="navigation">
        <div class="left-menu">
          <ul>
            <li><a href="<?php echo getSetting('URL', $APIToken);?>Why-Us">Why Us?</a></li>
            <li><a href="<?php echo getSetting('URL', $APIToken);?>Pricing">Pricing</a></li>
            <li><a href="<?php echo getSetting('URL', $APIToken);?>What-is-a-Virtual-Office">What is VO?</a></li>
            <li><a href="<?php echo getSetting('URL', $APIToken);?>Services">Our Services</a></li>
            <li><a href="<?php echo getSetting('URL', $APIToken);?>Locations">Locations</a></li>
            <?php 
					if(getSetting('ShowBlog', $APIToken))
					{
						echo'<li><a href="'.getSetting('URL', $APIToken).'Blog">Blog</a></li>';	
					}					
					?>
            <li><a href="<?php echo getSetting('URL', $APIToken);?>Contact">Contact Us</a></li>
          </ul>
        </div>
        <p><a href="<?php echo getSetting('URL', $APIToken);?>Signup"><img src="<?php echo getSetting('URL', $APIToken);?>App_Theme/<?php echo getSetting('Theme', $APIToken);?>/images/btn-signup-full.gif" alt="<?php echo getSetting('ShortCompanyName', $APIToken);?> Sign Up" width="179" height="40" border="0" /></a></p>
        <p><a href="<?php echo getSetting('URL', $APIToken);?>Pricing"><img src="<?php echo getSetting('URL', $APIToken);?>App_Theme/<?php echo getSetting('Theme', $APIToken);?>/images/btn-pricing.gif" alt="<?php echo getSetting('ShortCompanyName', $APIToken);?> Pricing and Plans" width="179" height="40" border="0" /></a></p>
      </div>
      <div class="clear"></div>
    </div>
  </div>
  <?php include("App_Includes/footer.php");?>
</div>

<?php include("App_Includes/HubspotTag.php");?>

</body>
</html>
