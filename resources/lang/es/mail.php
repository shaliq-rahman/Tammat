<?php 



return [

    

    	// mail_footer

	'mail_footer_content'            => ':appName,<br />Sell, buy, exchange or just give a way near you and in a simple and entertaining way.',

	

	 
	// email_verification

    'email_verification_title' => 'Confirme su dirección de correo electrónico',

    'email_verification_action' => 'Confirme su dirección de correo electrónico',

    'email_verification_content_1' => 'Hola :userName !',

    'email_verification_content_2' => 'Haga clic en el botón de abajo para verificar su dirección de correo electrónico.',

    'email_verification_content_3' => '¿El botón no funciona? Pegue el siguiente enlace en su navegador:<br><a href=":verificationLink">:verificationLink</a>.',

    'email_verification_content_4' => '<br><br>Está recibiendo este ónico porquenta de Tammat o agregó una nueva dirección de correo electrónico. Si no fue usted, ignore este correo electrónico.',

    'email_verification_content_5' => '<br><br>Saludos cordiales,<br>El equipo de Tammat',

	

	// post_activated

	'post_activated_title'             => 'Your Ad is active',

	'post_activated_content_1'         => 'Dear :userName,<br> Your ad entitled :title and has ID No. :postNumber has been reviewed and approved for publishing. The current status of your Ad is Active. However, your Ad will be expired after 30 days if you already purchased premium plan and 10 days if you Chose free plan. Still you can upgrade your ad by editting the post and make the payment after you change the plan to premium.',

	'post_activated_content_2'         => '<br>Thank you for using :appName <br /><br /> [:appName] Team',

	'post_activated_content_3'         => '<br><br>You’re receiving this email because you recently created a new ad on :appName. If this wasn’t you, please ignore this email.',

	'post_activated_content_4'         => '<br><br>Kind Regards,<br /><br /> :appName Team',



	

	// post_reviewed

	'post_reviewed_title'              => 'Your Ad is published',

	'post_reviewed_content_1'          => 'Dear :userName,<br><br> Your ad entitled :title and has ID No. :postNumber is published and its status is Active. Your Ad will be expired after 30 days if you purchased premium plan and 10 days otherwise',

	'post_reviewed_content_2'          => '<br><br>Thank you for using Tammat <br><br> Tammat Team.',

	'post_reviewed_content_3'          => '<br><br>Kind Regards,<br/><br/> :appName Team',



		
 
	// user_post_notification

	'user_post_notification_title'          => 'Your new Ad is being reviewd and waiting for approval',

	'user_post_notification_content_1'      => 'Dear :userName,<br> Your new Ad entitled :title and has ID No. :postNumber is in pending status and being reviewed by one of :appName team. After completing the reviewing process, your Ad will either be:',

	'user_post_notification_content_2'      => '<br>1. Approved for publishing and change to active status.<br>2. Rejected and changed to status which you will need to make changes and try again to get it approved.<br><br>Signature: Thank you for using :appName <br /><br /> :appName Team',

	

		

	// post_rejected
 
	'post_rejected_title'              => 'Your Ad is rejected',

	'post_rejected_content_1'          => 'Dear :userName,<br> Your ad entitled :title and has ID No. :postNumber has been rejected and assigned to rejected status due to one or more of the followings:',

	'post_rejected_content_2'          => '<br>1. It violates :appName regulations regarding dealing with forbidden items such as unregulated medicine, drugs, prostitution, pornography, unlicensed weapons ...etc. <br> 2. Your ad contains political issues which will create a conflict with a government. <br> 3. Doubt that your Ad is a fraud. <br> 4. Your item is stolen. <br> 5. A reason :appName don’t want to reveal to you.',

	'post_rejected_content_3'          => '<br><br>Kind Regards,<br /><br />:appName Team',

	

	

	// post_deleted

	'post_deleted_title'               => 'Your Ad is deleted',

	'post_deleted_content_1'           => 'Dear :userName,<br> Your ad entitled :title and has ID No. :postNumber has been deleted from your account at <a href=":appUrl">:appName</a> at :now.',

	'user_post_deleted_content_1'      => 'Dear :userName,<br> your ad entitled :title and NO. :postNumber has been deleted from your account',

	'post_deleted_content_2'           => '<br>Thank you for using :appName <br /><br /> :appName Team',

	'post_deleted_content_3'           => '<br><br> :appName Team',

	'post_deleted_content_4'           => '<br><br><br>PS: This is an automated email, please don\'t reply.',

	

	

	// post_seller_contacted

	'post_seller_contacted_title'      => 'You have a new message regarding your Ad ":title"',

	'post_seller_contacted_title_delivey_info'      => 'Delivey Info regrding your Ad ":title"',

	

	'post_seller_contacted_content_1'  => '<strong>Contact Information :</strong><br>Name : :name<br>Email address : :email<br>Phone number : :phone<br>Message : :message<br><br><strong>Delivery Preference:</strong> <br> :delivery_preference<br><br><strong>Date & Time Preference:</strong> <br> :date_time<br><br><strong>Buyer Address:</strong> <br> :buyer_address<br><br>This email was sent to you about the ad ":title" you filed on <a href=":appUrl">:appName</a> : <a href=":postUrl">:postUrl</a>',

	'post_seller_contacted_content_2'  => '<br><br>PS : The person who contacted you do not know your email. He needs your reply to have your contact information.',

	'post_seller_contacted_content_3'  => '<br><br>Remember to always check the details of your contact person (name, address, ...) to ensure you have a contact infromation in case of dispute. In general, choose the delivery for the item in hand.<br><br>Beware of enticing offers! Be careful with requests from abroad when you only have a contact email. The bank transfer by Western Union or MoneyGram proposed may well be artificial.',

	'post_seller_contacted_content_4'  => '<br><br>Thank you for your trust and see you soon,',

	'post_seller_contacted_content_5'  => '<br><br>:appName Team',

	'post_seller_contacted_content_7'  => '<b><br><br>The delivery service is provided by a third party where Tammat no control over, and assumes no responsibility or liability for, the practices of any third party.<br><br>Please check the item post for to know who will be charged for the delivery cost.</b>',

	

	'post_seller_contacted_content_6'  => '<br><br><br>PS: This is an automated email, please don\'t reply.',

	

	

	// user_deleted

	'user_deleted_title'             => 'Your account has been closed',

	'user_deleted_content_1'         => 'Hello,<br>Your account has been closed on <a href=":appUrl">:appName</a> at :now.',

	'user_deleted_content_2'         => '<br><br>Thank you for your trust and see you soon,',

	'user_deleted_content_3'         => '<br><br> :appName Team',

	'user_deleted_content_4'         => '<br><br><br>PS: This is an automated email, please don\'t reply.',

	

	

	// user_activated

	'user_activated_title'           => 'Welcome to Tammat!',

	'user_activated_content_1'       => 'Welcome to :appName :userName !',

	'user_activated_content_2'       => '<br>Your account has been activated successfully.',

	'user_activated_content_3'       => '<br><br><strong>Note : :appName team recommends that you:</strong><br><br>1 - Always beware of advertisers refusing to make you see the good offers for sale or rent,<br>2 - Never send money by Western Union or other international mandate.<br><br>3 - If you have any doubt about the seriousness of an advertiser, please contact us immediately. We can then neutralize as quickly as possible and prevent someone less informed do become the victim.',

	'user_activated_content_4'       => '<br><br>You’re receiving this email because you recently created a new :appName account. If this wasn’t you, please ignore this email.',

	'user_activated_content_5'       => '<br><br>Kind Regards,<br /><br /> :appName Team',

	

	

	// reset_password

    'reset_password_title' => 'Reiniciar Contraseña',

    'reset_password_action' => 'Reiniciar Contraseña',

    'reset_password_content_1' => '¿Olvidó su contraseña?',

    'reset_password_content_2' => 'Vamos a crear una nueva.',

    'reset_password_content_3' => 'Si no solicitó reiniciar su contraseña, ignore este correo electrónico.',

    'reset_password_content_4' => '<br><br>Saludos,<br>:appName',

    'reset_password_content_5' => '<br><br>---<br>Si tiene problemas para haga clic en el botón "Restablecer contraseña", copie y pegue la URL a continuación en su navegador web:<br> :link',	

	

	

	// contact_form

	'contact_form_title'             => 'New message from :appName',

	'contact_form_content'           => ':appName - New message',

	

	

	// post_report_sent

	'post_report_sent_title'           => 'New abuse report',

	'post_report_sent_content'         => 'New Report Abuse - :appName/:countryCode',

	'Post URL'                         => 'Post URL',

	

	

	// post_archived

	'post_archived_title'             => 'Your Ad has been moved to your Archive',

	'post_archived_content_1'         => 'Dear :userName,<br> Your ad entitled :title and has ID No. :postNumber has been moved to your archive. You can repost your Ad from your archive without need for review or approval, unless you make a change to the post which then will be treated as a new post. Your Ad will be deleted after limited time if you do not repost it.',

	'post_archived_content_2'         => '<br><br>Signature: Thank you for using :appName <br /><br /> :appName Team',

	'post_archived_content_3'         => '<br><br>You’re receiving this email because you recently created a new ad on :appName. If this wasn’t you, please ignore this email.',

	'post_archived_content_4'         => '<br><br>Kind Regards,<br /><br /> :appName Team',

	

	

	// post_will_be_deleted

	'post_will_be_deleted_title'       => 'Your ad will be deleted in :days days',

	'post_will_be_deleted_content_1'   => 'Hello,<br><br>Your ad ":title" and has ID No. :postNumber, you can find it in your archived, will be deleted in :days days from :appName.',

	'post_will_be_deleted_content_2'   => '<br><br>You can repost it by clicking here : <a href=":repostLink">:repostLink</a>',

	'post_will_be_deleted_content_3'   => '<br><br>If you do nothing your ad will be permanently deleted on :dateDel.',

	'post_will_be_deleted_content_4'   => '<br><br>Thank you for your trust and see you soon,',

	'post_will_be_deleted_content_5'   => '<br><br>The :appName Team',

	'post_will_be_deleted_content_6'   => '<br><br><br>PS: This is an automated email, please don\'t reply.',

	

	

	// post_notification

	'post_notification_title'          => 'New ad has been posted',

	'post_notification_content_1'      => 'Hello Admin,<br>The user has just posted a new ad.',

	'post_notification_content_2'      => '<br>The ad title: :title <br>Posted on: :now at :time',

	'post_notification_content_3'      => '<br><br>Kind Regards,<br /><br /> :appName Team',

	





	// user_notification

	'user_notification_title'        => 'New User Registration',

	'user_notification_content_1'    => 'Hello Admin,<br><br>:name has just registered.',

	'user_notification_content_2'    => '<br>Registered on: :now at :time<br>Email: <a href="mailto::email">:email</a>',

	'user_notification_content_3'    => '<br><br>Kind Regards,<br /><br /> :appName Team',

	

	

	// payment_sent

	'payment_sent_title'             => 'Thanks for your payment!',

	'payment_sent_content_1'         => 'Hello :name,<br><br>Your payment for the ad is done! ":title" & ID ":postNumber".',

	'payment_sent_content_2'         => '<br><h1>Thank you!</h1>',

	'payment_sent_content_3'         => '<br>Kind Regards,<br /><br /> :appName Team',

	

	

	// payment_notification

	'payment_notification_title'     => 'New payment has been sent',

	'payment_notification_content_1' => 'Hello Admin,<br><br>The user :advertiserName ":name" has just paid a package for the ad ":title".',

	'payment_notification_content_2' => '<br><br><strong>THE PAYMENT DETAILS</strong><br><strong>Reason of the payment:</strong> Ad #:adId - :packageName<br><strong>Amount:</strong> :amount :currency<br><strong>Payment Method:</strong> :paymentMethodName',

	'payment_notification_content_3' => '<br><br>Kind Regards,<br /><br /> :appName Team',

	

	

	// reply_form

	'reply_form_title'               => ':subject',

	'reply_form_content_1'           => 'Hello,<br><br><strong>You have a response from: :senderName. See the message below:</strong><br><br>',

	'reply_form_content_2'           => '<br><br>Kind Regards,<br> :appName Team',





    // send offer

    'new_offer'                  =>'[:appName] New Offer',

    'offer_send_1'               => 'Hello :sellername',

    'offer_send_2'               => '<br><br>You have received a response from :buyername. See the message below:',

    'offer_send_3'               => '<br><br>You have received New Offer from :buyername',

    'offer_send_4'               => 'Related to the ad: <a href=":url">Click here to see</a>',

    'offer_send_5'               => '<br><br>Kind Regards,<br /><br /> :appName Team',

    

    

    // offer rejected

    'offer_rejected'               =>'[:appName] Your Offer is Rejected',

    'offer_reject_1'               => 'Hello :toname',

    'offer_reject_2'               => '<br><br>You have received a response from :fromname. See the message below:',

    'offer_reject_3'               => 'Your offer is Rejected by :fromname',

    'offer_reject_4'               => 'Related to the ad: <a href=":url">Click here to see</a>',

    'offer_reject_5'               => '<br><br>Kind Regards,<br /><br /> :appName Team',

    

    

        

    // Expired Ad

    'expire_post'               =>'[:appName] Expired Ad',

    'expire_post_1'               => 'Hello :toname,',

    'expire_post_2'               => '<br>Your Ad is expired and moved to your archive. You can repost your Ad from your archive without need for review or approval, unless you make a change to the post which then will be treated as a new post. Your Ad will be deleted after limited time if you do not repost it.',

    'expire_post_3'               => 'Post Title: :title',

    'expire_post_5'               => 'https://www.tmmat.com',

    'expire_post_4'               => '<br><br>Kind Regards,<br /><br /> :appName Team',



    

    

];



