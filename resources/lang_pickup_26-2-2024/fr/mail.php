<?php 



return [





	// mail_footer

    'mail_footer_content' => ':appName, vendez et achetez près de chez vous. Simple, rapide et efficace.',	

	
Tammat
	

	// email_verification

    'email_verification_title' => 'Vérifier votre adresse email.',

    'email_verification_action' => 'Vérifiez votre adresse email',

    'email_verification_content_1' => 'Bonjour :userName !',

    'email_verification_content_2' => 'Cliquez sur le bouton ci-dessous pour vérifier votre adresse email.',

    'email_verification_content_3' => 'Le bouton ne marche pas? Copiez le lien suivant dans votre navigateur:<br><a href=":verificationLink">:verificationLink</a>.',

    'email_verification_content_4' => '<br><br>Vous recevez cet email car vous avez récemment créé un nouveau compte Deal Not Deal ou vous avez ajouté une nouvelle adresse email. Si ce n\'était pas vous, veuillez ignorer cet email.',

    'email_verification_content_5' => '<br><br>Cordialement,,<br>L\'équipe :appName',

	

	

	// post_activated

    'post_activated_title' => 'Votre annonce a bien été activée',

    'post_activated_content_1' => 'Bonjour, <br><br>Votre annonce <a href=":postUrl">:title</a> a bien été activée.',

    'post_activated_content_2' => '<br>Elle sera prochainement examinée par un de nos administrateurs pour sa mise en ligne.',

    'post_activated_content_3' => '<br><br>Vous recevez ce message parce que vous avez récemment publié une nouvelle annonce sur :appName. Nous vous prions d\'ignorer ce message si nous nous sommes trompés.',

    'post_activated_content_4' => '<br><br>Cordialement,<br>L\'équipe :appName',





	// post_reviewed

    'post_reviewed_title' => 'Votre annonce est maintenant en ligne',

    'post_reviewed_content_1' => 'Bonjour, <br><br>Votre annonce <a href=":postUrl">:title</a> is now online.',

    'post_reviewed_content_2' => '<br><br>Vous recevez ce message parce que vous avez récemment publié une nouvelle annonce sur :appName. Nous vous prions d\'ignorer ce message si nous nous sommes trompés.',

    'post_reviewed_content_3' => '<br><br>Cordialement,<br>L\'équipe :appName',

		Tammat

		

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

    'post_deleted_title' => 'Votre annonce a bien été supprimée',

    'post_deleted_content_1' => 'Bonjour,<br><br>Votre annonce ":title" a bien été supprimée de <a href=":appUrl">:appName</a> le :now.',

    'post_deleted_content_2' => '<br><br>Merci de votre confiance et à très bientôt sur notre site,',

    'post_deleted_content_3' => '<br><br>L\'équipe :appName',

    'post_deleted_content_4' => '<br><br><br>PS: Ceci est un email automatique, merci de ne pas y répondre.',

	

	

	// post_seller_contacted

    'post_seller_contacted_title'     => 'Votre annonce ":title" sur :appName',

    'post_seller_contacted_title_delivey_info' => 'Delivey Info ":title" on :appName',

    'post_seller_contacted_content_1' => '<strong>Coordonnées du contact :</strong><br>Nom : :name<br>Email : :email<br>Tel : :phone<br><br>Cet email vous a été envoyé au sujet de l\'annonce ":title" que vous avez déposée sur <a href=":appUrl">:appName</a> : <a href=":postUrl">:postUrl</a>',

    'post_seller_contacted_content_2' => '<br><br>PS : la personne qui vous a contacté ne connaîtra pas votre email tant que vous ne lui aurez pas répondu.',

    'post_seller_contacted_content_3' => '<br><br>Pensez à toujours vérifier les coordonnées de votre interlocuteur (nom, prénom, adresse, ...) afin de vous assurer d\'avoir un contact en cas de litige. D\'une manière générale, privilégiez la remise de l\'objet en mains propres.<br><br>Méfiez-vous des offres trop alléchantes ! Soyez vigilants avec les demandes provenant de l\'étranger quand vous ne disposez que d\'un contact par email. Le virement bancaire par Western Union ou Mandat Cash proposé risque bien d\'être factice.',

    'post_seller_contacted_content_4' => '<br><br>Merci de votre confiance et à très bientôt sur notre site,',

    'post_seller_contacted_content_5' => '<br><br>L\'équipe :appName',

    'post_seller_contacted_content_6' => '<br><br><br>PS: Ceci est un email automatique, merci de ne pas y répondre.',

    'post_seller_contacted_content_7' => '<b><br><br>The delivery service is provided by a third party where Deal Not Deal no control over, and assumes no responsibility or liability for, the practices of any third party.<br><br>Please check the item post for to know who will be charged for the delivery cost.</b>',



	

	// user_deleted

    'user_deleted_title' => 'Votre compte a bien été supprimé',

    'user_deleted_content_1' => 'Bonjour,<br><br>Votre compte a bien été supprimée de <a href=":appUrl">:appName</a> le :now.',

    'user_deleted_content_2' => '<br><br>Merci de votre confiance et à très bientôt sur notre site,',

    'user_deleted_content_3' => '<br><br>L\'équipe :appName',

    'user_deleted_content_4' => '<br><br><br>PS: Ceci est un email automatique, merci de ne pas y répondre.',

	

	

	// user_activated

    'user_activated_title' => 'Bienvenu(e) sur :appName !',

    'user_activated_content_1' => 'Bienvenu(e) sur :appName :userName !',

    'user_activated_content_2' => '<br>Votre compte a bien été activé.',

    'user_activated_content_3' => '<br><br><strong>Attention, l\'équipe de :appName vous recommande de :</strong><br><br>1 - Toujours se méfier des annonceurs refusant de vous faire voir le bien mis en vente ou en location,<br>2 - Ne jamais envoyer d\'argent par Western Union ou autre mandat international.<br><br>Si vous avez un doute concernant le sérieux d\'un annonceur, contactez-nous immédiatement. Nous pourrons ainsi le neutraliser au plus vite et éviter qu\'une personne moins avisée n\'en devienne la victime.',

    'user_activated_content_4' => '<br><br>Vous recevez ce message parce que vous avez récemment créé un nouveau compte :appName. Nous vous prions d\'ignorer ce message si nous nous sommes trompés.',

    'user_activated_content_5' => '<br><br>Cordialement,<br>L\'équipe :appName',



	

	// reset_password

    'reset_password_title' => 'Réinitialiser le mot de passe',

    'reset_password_action' => 'Réinitialiser le mot de passe',

    'reset_password_content_1' => 'Mot de passe oublié?',

    'reset_password_content_2' => 'On va en trouver un autre!',

    'reset_password_content_3' => 'Si vous n\'êtes pas à l\'origine de cette réinitialisation de mot de passe, aucune autre mesure n\'est requise.',

    'reset_password_content_4' => '<br><br>Cordialement,<br>:appName',

    'reset_password_content_5' => '<br><br>---<br>Si vous avez des difficultés à cliquer sur le bouton "Réinitialiser le mot de passe" veuillez copier-coller l\'URL ci-dessous dans votre navigateur:<br> :link',

	

	

	// contact_form

    'contact_form_title' => 'Nouveau message de :appName',

    'contact_form_content' => ':appName - Nouveau message',

	

	

	// post_report_sent

    'post_report_sent_title' => 'Nouveau report d\'abus',

    'post_report_sent_content' => 'Nouveau report d\'abus - :appName/:countryCode',

    'Post URL' => 'URL de l\'annonce',

	

	

	// post_archived

    'post_archived_title' => 'Votre annonce a été archivée',

    'post_archived_content_1' => 'Bonjour,<br><br>Votre annonce ":title" a été archivé sur :appName le :now.',

    'post_archived_content_2' => '<br><br>Vous pouvez la re-publier en cliquant sur ce lien: <a href=":repostLink">:repostLink</a>',

    'post_archived_content_3' => '<br><br>Si vous ne faites rien votre annonce sera définitivement supprimée le :dateDel.',

    'post_archived_content_4' => '<br><br>Merci de votre confiance et à très bientôt sur notre site,',

    'post_archived_content_5' => '<br><br>Meilleures salutations,<br>L\'équipe :appName',

    'post_archived_content_6' => '<br><br><br>PS: Ceci est un email automatique, merci de ne pas y répondre.',



	

	// post_will_be_deleted

    'post_will_be_deleted_title' => 'Votre annonce sera supprimée dans :days jours',

    'post_will_be_deleted_content_1' => 'Bonjour,<br><br>Votre annonce ":title" sera supprimée dans :days jours de :appName.',

    'post_will_be_deleted_content_2' => '<br><br>Vous pouvez la re-publier en cliquant sur ce lien: <a href=":repostLink">:repostLink</a>',

    'post_will_be_deleted_content_3' => '<br><br>Si vous ne faites rien votre annonce sera définitivement supprimée le :dateDel.',

    'post_will_be_deleted_content_4' => '<br><br>Merci de votre confiance et à très bientôt sur notre site,',

    'post_will_be_deleted_content_5' => '<br><br>Meilleures salutations,<br>L\'équipe :appName',

    'post_will_be_deleted_content_6' => '<br><br><br>PS: Ceci est un email automatique, merci de ne pas y répondre.',

	

	

	// post_notification

    'post_notification_title' => 'Une annonce vient d\'être posté,',

    'post_notification_content_1' => 'Bonjour Admin,<br><br>L\'utilisateur :advertiserName vient de poster une nouvelle annonce.',

    'post_notification_content_2' => '<br>Titre de l\'annonce: :title<br>Publiée le: :now à :time',

    'post_notification_content_3' => '<br><br>Meilleures salutations,<br>L\'équipe :appName',





	// user_notification

    'user_notification_title' => 'Un nouvel utilisateur',

    'user_notification_content_1' => 'Bonjour Admin,<br><br>:name vient de s\'inscrire.',

    'user_notification_content_2' => '<br>Inscrit le: :now à :time<br>Email: <a href="mailto::email">:email</a>',

    'user_notification_content_3' => '<br><br>Meilleures salutations,<br>L\'équipe :appName',

	

	// payment_sent

	'payment_sent_title' => 'Merci pour votre paiement !',

    'payment_sent_content_1' => 'Bonjour,<br><br>Nous avons bien reçu votre paiement pour l\'annonce ":title".',

    'payment_sent_content_2' => '<br><h1>Merci !</h1>',

    'payment_sent_content_3' => '<br>Meilleures salutations,<br>L\'équipe :appName',



	

	// payment_notification

    'payment_notification_title' => 'Un paiement vient d\'être effectué',

    'payment_notification_content_1' => 'Bonjour Admin,<br><br>L\'utilisateur :advertiserName vient de payer un package pour son annonce ":title".',

    'payment_notification_content_2' => '<br><br><strong>DETAILS DU PAIEMENT</strong><br><strong>Motif du paiement:</strong> Annonce #:adId - :packageName<br><strong>Montant:</strong> :amount :currency<br><strong>Moyen de paiement:</strong> :paymentMethodName',

    'payment_notification_content_3' => '<br><br>Meilleures salutations,<br>L\'équipe :appName',	

	

	

	// reply_form

    'reply_form_title' => ':subject',

    'reply_form_content_1' => 'Bonjour,<br><br><strong>Vous avez reçu une réponse de: :senderName. Voir le message ci-dessous:</strong><br><br>',

    'reply_form_content_2' => '<br><br>Meilleures salutations,<br>L\'équipe :appName',





    // send offer

    'new_offer'                  =>'New Offer',

    'offer_send_1'               => 'Hello :sellername',

    'offer_send_2'               => '<br><br>You have received a response from :buyername. See the message below:',

    'offer_send_3'               => '<br><br>You have received New Offer from :buyername',

    'offer_send_4'               => 'Related to the ad: <a href=":url">Click here to see</a>',

    'offer_send_5'               => '<br><br>Kind Regards,<br /><br /> :appName Team',

    

    

    // offer rejected

    'offer_rejected'               =>'Your Offer is Rejected',

    'offer_reject_1'               => 'Hello :toname',

    'offer_reject_2'               => '<br><br>You have received a response from :fromname. See the message below:',

    'offer_reject_3'               => 'Your offer is Rejected by :fromname',

    'offer_reject_4'               => 'Related to the ad: <a href=":url">Click here to see</a>',

    'offer_reject_5'               => '<br><br>Kind Regards,<br /><br /> :appName Team',

    

    

        

    // Expired Ad

    'expire_post'               =>'Expired Ad',

    'expire_post_1'               => 'Hello :toname,',

    'expire_post_2'               => '<br>Your Ad is expired and moved to your archive. You can repost your Ad from your archive without need for review or approval, unless you make a change to the post which then will be treated as a new post. Your Ad will be deleted after limited time if you do not repost it.',

    'expire_post_3'               => 'Post Title: :title',

    'expire_post_5'               => 'https://www.tmmat.com',

    'expire_post_4'               => '<br><br>Kind Regards,<br /><br /> :appName Team',





];

