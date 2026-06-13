<p><b>{{t('Subject')}} :</b>  <a href="<?=$seller_product_title_url?>"><?php echo $postsubject; ?></a></p>

<p><b>{{t('Buyer Username')}} :</b> <?php echo $buyername; ?></p>
<p><b>{{t('Buyer Phone')}} :</b> <?php echo $buyerphone; ?></p>
<p><b>{{t('Buyer Address')}} :</b> <?php echo $buyer_address; ?></p>
<p><b>{{t('Date & Time Preference')}} :</b> <?php echo $DateTimePreference; ?></p>
@if(!empty($buyer_offer_price))
<p><b>Buyer Price :</b> <?php echo $buyer_offer_price; ?></p>
@endif
@if(!empty($buyer_product_1_title))
<p><b>Buyer Product Title :</b> <a href="<?=$buyer_product_1_title_url?>"><?php echo $buyer_product_1_title; ?></a></p>
@endif
@if(!empty($buyer_product_2_title))
<p><b>Buyer Product Title :</b> <a href="<?=$buyer_product_2_title_url?>"><?php echo $buyer_product_2_title; ?></a></p>
@endif
@if(!empty($buyer_product_3_title))
<p><b>Buyer Product Title :</b> <a href="<?=$buyer_product_3_title_url?>"><?php echo $buyer_product_3_title; ?></a></p>
@endif

<p>**************************************</p>

<p><b>{{t('Seller Username')}} :</b> <?php echo $sellerusername; ?></p>
<p><b>{{t('Seller Address')}} :</b> <?php echo $seller_address; ?></p>
<p><b>{{t('Date of pick up the item from the seller')}} :</b> <?php echo $dateofpick; ?></p>
@if(!empty($seller_price))
<p><b>Seller Price :</b> <?php echo $seller_price; ?></p>
@endif
@if(!empty($postsubject))
<p><b>Seller Product Title :</b> <a href="<?=$seller_product_title_url?>"><?php echo $postsubject; ?></a></p>
@endif
@if(!empty($seller_product_1_title))
<p><b>Seller Product Title :</b> <a href="<?=$seller_product_1_title_url?>"><?php echo $seller_product_1_title; ?></a></p>
@endif
@if(!empty($seller_product_2_title))
<p><b>Seller Product Title :</b> <a href="<?=$seller_product_2_title_url?>"><?php echo $seller_product_2_title; ?></a></p>
@endif
<p><b>{{t('Message')}} :</b> <?php echo $message_string; ?></p>

<br />
<b>
<p>
   {{ t('The delivery service is provided by a third party where Tammat no control over, and assumes no responsibility or liability for, the practices of any third party.') }}
</p>
<p>
        {{ t('Please check the item post for to know who will be charged for the delivery cost.') }}
</p>
</b>


