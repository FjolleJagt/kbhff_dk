<?php
global $action;
global $model;

$user_id = session()->value("user_id");

// if cart reference was passed to cart controller
if(count($action) > 1) {
	session()->value("cart_reference", $action[1]);
}
$cart = $model->getCart();
$cart_id = $cart ? $cart["id"] : false;


$IC = new Items();
$UC = new User();

?>
<div class="scene cart i:cart">
	<h1>Din kurv</h1>
	<?
	//print all stored messages
	print $HTML->serverMessages();
	?>
	<? 
	if($cart && $cart["items"]) :

		// Get the total cart price
		$total_cart_price = $model->getTotalCartPrice($cart_id);
		
		if($total_cart_price && $total_cart_price["price"] > 0) {
			
			// Get payment methods
			$payment_methods = $this->paymentMethods();
			
			// Get payment methods
			$user_payment_methods = $UC->getPaymentMethods(["extend" => true]);
			
		}
		
		$cart_pickupdates = $model->getCartPickupdates();
		$cart_items_without_pickupdate = $model->getCartItemsWithoutPickupdate();
		
		if($user_id != 1) {
			$department = $UC->getUserDepartment();
		}

	?>
	<div class="checkout">
		<ul class="actions">
			<?= $HTML->oneButtonForm("Gå til betaling", "/butik/betal", array(
				"wait-value" => "Vent venligst",
				"confirm-value" => false,
				"dom-submit" => true,
				"success-location" => "/butik/betal",
				"class" => "primary",
				"name" => "continue",
				"wrapper" => "li.continue",
			)) ?>
		</ul>
	</div>
<? endif; ?>

	<div class="all_items">
		<? if($cart && $cart["items"]): ?>
		<h2>Kurven indeholder</h2>
		<? if($cart_items_without_pickupdate): ?>
		<ul class="items">
			<? 
			// Loop through all cart items and show information and editing options of each item.
			foreach($cart_items_without_pickupdate as $cart_item):
				$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true)));
				$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
			?>
			<li class="item id:<?= $item["id"] ?>">
				<h3>
					<?
					// add option of updating item quantity to item 
					print $model->formStart("/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
						<fieldset>
							<?= $model->input("quantity", array(
								"type" => "integer",
								"value" =>  $cart_item["quantity"],
								"label" => "Antal",
								"hint_message" => "State the quantity of this item"
							)) ?>
						</fieldset>
						<ul class="actions">
							<?= $model->submit("Opdatér", array("name" => "update", "wrapper" => "li.save")) ?>
						</ul>
					<?= $model->formEnd() ?>
					<span class="x">x </span>
					<span class="name"><?= $item["name"] ?> </span>
					<span class="a">á </span>
					<span class="unit_price"><?= formatPrice($price) ?></span>
					<span class="total_price">
						<? // generate total price and vat to item 
						print formatPrice(array(
								"price" => $price["price"]*$cart_item["quantity"],
								"vat" => $price["vat"]*$cart_item["quantity"],
								"currency" => $cart["currency"],
								"country" => $cart["country"]
							),
							array("vat" => true)
						) ?>
					</span>
				</h3>
				<? // print subscription information 
				if($item["subscription_method"] && $price["price"]): ?>
				<p class="subscription_method">
					Betaling gentages hver <?= strtolower($item["subscription_method"]["name"]) ?>.
				</p>
				<? endif; ?>

				<? // print membership information
				if($item["itemtype"] == "signupfee"): ?>
				<p class="membership">
					Dit køb inkluderer et medlemskab.
				</p>
				<? endif; ?>

				<ul class="actions">
					<? // generate delete button to item 
					print $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/".$cart_item["id"], array(
						"wrapper" => "li.delete",
						"static" => true
					)) ?>
				</ul>
			</li>
			<? endforeach; ?>
		</ul>
		<? endif; ?>
		<? if($cart_pickupdates): ?>
			<ul class="pickupdates">
					
				<? foreach($cart_pickupdates as $pickupdate): 
	
					$pickupdate_cart_items = $model->getCartPickupdateItems($pickupdate["id"]);
	
				?>
					<? if($pickupdate_cart_items): ?>
					
				<li class="pickupdate">
					<h4 class="pickupdate"><?= date("d/m-Y", strtotime($pickupdate["pickupdate"])) ?></h4>
					<p class="department">Afhentningssted: <span class="name"><?= $department["name"] ?></span></p>
					
					<ul class="items">
						
						<? foreach($pickupdate_cart_items as $cart_item):
						$item = $IC->getItem(array("id" => $cart_item["item_id"], "extend" => array("subscription_method" => true))); 
						$price = $model->getPrice($cart_item["item_id"], array("quantity" => $cart_item["quantity"], "currency" => $cart["currency"], "country" => $cart["country"]));
						$cart_item_id = $cart_item["id"];
						?>
	
						<li class="item id:<?= $item["id"] ?>">
							<p>
								<?= $model->formStart("/butik/updateCartItemQuantity/".$cart["cart_reference"]."/".$cart_item["id"], array("class" => "updateCartItemQuantity labelstyle:inject")) ?>
									<fieldset>
										<?= $model->input("quantity", array(
											"type" => "integer",
											"value" =>  $cart_item["quantity"],
											"label" => "Antal",
											"hint_message" => "State the quantity of this item"
										)) ?>
									</fieldset>
									<ul class="actions">
										<?= $model->submit("Opdatér", array("name" => "update", "wrapper" => "li.save")) ?>
									</ul>
								<?= $model->formEnd() ?>
								<span class="x">x </span>
								<span class="name"><?= $item["name"] ?> </span>
								<span class="a">á </span>
								<span class="unit_price"><?= formatPrice($price, ["conditional_decimals" => true]) ?></span>
							</p>
							<ul class="actions">
								<?= $HTML->oneButtonForm("Slet", "/butik/deleteFromCart/".$cart["cart_reference"]."/$cart_item_id", [
									"confirm-value" => "Sikker?",
									"wrapper" => "li.delete",
									"success-location" => "/butik"
									]) ?>
							</ul>
						</li>
	
						<? endforeach; ?>
					</ul>
				</li>
	
					<? endif; ?>
				<? endforeach; ?>
			</ul>
			<div class="total">
				<h3>
					<span class="name">I alt</span>
					<span class="total_price">
						<?= formatPrice($total_cart_price) ?>
					</span>
				</h3>
			</div>
		<? endif; ?>
		
		<? else: ?>
		<h2>Din indkøbskurv er tom</h2>
		<p>Gå til <a href="/bliv-medlem">medlemskaber </a>for at se, hvad vi tilbyder.</p>
		<ul class="items">
			<li class="total">
				<h3>
					<span class="name">Total</span>
					<span class="total_price">
						<?= formatPrice($model->getTotalCartPrice($cart_id), array("vat" => true)) ?>
					</span>
				</h3>
			</li>
		</ul>
		<? endif; ?>
	</div>

	<? // Generate checkout button
	if($cart && $cart["items"]) :?>
	<div class="checkout">
		<ul class="actions">
			<?= $HTML->oneButtonForm("Gå til betaling", "/butik/betal", array(
				"confirm-value" => false,
				"wait-value" => "Vent venligst",
				"dom-submit" => true,
				"class" => "primary",
				"name" => "continue",
				"wrapper" => "li.continue",
			)) ?>
		</ul>
	</div>
<? 	endif; ?>
</div>
