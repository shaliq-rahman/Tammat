<?php 



return [



	// mail_footer

    'mail_footer_content' => 'Tammat, بيع وأشتري وبادل على مقربة منك ببساطة وكفاءة.',	



	

	// email_verification

    'email_verification_title' => 'الرجاء السماح لنا بالتحقق من بريدك الالكتروني.',

    'email_verification_action' => 'التحقق من عنوان البريد الإلكتروني',

    'email_verification_content_1' => 'مرحبا :userName !',

    'email_verification_content_2' => 'أضغط على الزر للتحقق من عنوان بريدك الإلكتروني.',

    'email_verification_content_3' => 'في حالة الزر لا يعمل؟ الصق الرابط التالي في متصفحك:<br><a href=":verificationLink">:verificationLink</a>.',

    'email_verification_content_4' => '<br><br>أنت تتلقى هذا البريد الإلكتروني لأن عنوان البريد الإلكتروني الجديد تخطى Deal No Deal حسابأو إضافة عنوان بريد إلكتروني جديد. إذا لم تكن كذلك ، فيرجى تجاهل هذا البريد الإلكتروني',

    'email_verification_content_5' => '<br><br>أطيب التحيات,<br>فريق Tammat ',

	

	

	// post_activated

    'post_activated_title'             => 'لقد تم نشر إعلانك',

	'post_activated_content_1'         => 'Dear :userName,<br> Your ad entitled :title and has ID No. :postNumber has been reviewed and approved for publishing. The current status of your Ad is Active. However, your Ad will be expired after 30 days if you already purchased premium plan and 10 days if you Chose free plan. Still you can upgrade your ad by editting the post and make the payment after you change the plan to premium.',

	'post_activated_content_2'         => '<br>Thank you for using :appName <br /><br /> [:appName] Team',

	'post_activated_content_3'         => '<br><br>You’re receiving this email because you recently created a new ad on :appName. If this wasn’t you, please ignore this email.',

	'post_activated_content_4'         => '<br><br>Kind Regards,<br /><br /> :appName Team',    





	// post_reviewed

	'post_reviewed_title'              => 'تم نشر أعلانك والموافقة عليه',

	'post_reviewed_content_1'          => 'عزيزي :userName,<br> أعلانك بعنوان :postTitle ويحمل كود رقم :postNumberوللعلم فإن صلاحيتك أعلانك هي 30 يوم في حالة قمت بشراء باقة الأعلان المميز و 10 أيام للباقة المجانية .تم نشره والموافقة عليه ويعتبر نشط. ',

	'post_reviewed_content_2'          => '<br> شكراً لأستخدامك Tammat <br> Tammat فريق.',

	'post_reviewed_content_3'          => '<br><br>مع أخلص التحيات,<br> :appName Team',



    	

	// user_post_notification

	'user_post_notification_title'          => 'إعلانك الجديد في طور المراجعة وبانتظار الموافقة',

	'user_post_notification_content_1'      => 'عزيزي :userName,<br> إعلانك الجديد بعنوان :postTitle ويحمل كود رقم :postNumber بانتظار الموافقة بعد مراجعته من قبل أحد من فريق Tammat team. نود أن نحيطك علما بأن بعد مراجعة إعلانك سوف يعتبر يدرج إعلانك في أحد الأحوال التالية:',

	'user_post_notification_content_2'      => '<br>1. يتم الموافقة على النشر وبذلك تصبح حالته نشط.<br>2.  يتم رفضه لمخالفته أحد الشروط وبذلك يضع في حالة رفض لتتمكن من تصحيح الخطأ ومطابقة الشروط والأحكام”<br>شكرا لاستخدامك Tammat <br> Tammat فريق',



    

    	// post_rejected

	'post_rejected_title'              => 'تم رفض نشر إعلانك',

	'post_rejected_content_1'          => 'Dear :userName,<br> إعلانك بعنوان :title ويحمل رقم :postNumings: قد تم رفض نشره ووضعه حتى حالة مرفوض وذلك لسبب أو أكثر من الأسباب التالية',

	'post_rejected_content_2'          => '<br>1. يخالف قوانين :appName والخاصة بالتعامل مع الأسحلة الغير مرخصة أو المخدرات أوالأدوية الممنوعة أو المتجارة بالجنس . <br> 2. إعلانك يحتوي على أمور سياسية تتضاربك مع قوانين ومصالح البلد والحكومة. <br> 3.هناك اعتقاد بأن إعلانك مزيف ومرتبط بعملية سرقة. <br> 4. سلعتك المعروضة مسروقة. <br> 5. أي سبب يرفض الإفصاح عنه من قبل [:appName].',

	'post_rejected_content_3'          => '<br><br>مع أخلص التحيات,<br /><br />:appName فريق',

	

	

		// post_deleted

	'user_post_deleted_content_1'      => 'Dear :userName,<br> your ad entitled :title and NO. :postNumber has been deleted from your account',

	'post_deleted_title' => 'تم حذف إعلانك',

    'post_deleted_content_1' => 'مرحباً,<br><br>إعلانك بعنوان ":title" قد تم حذفه <a href=":appUrl">Tammat</a> بتوقيت :now.',

    'post_deleted_content_2' => '<br><br>شكراً على ثقتك وإستخدامك :appName,',

    'post_deleted_content_3' => '<br><br>Tammat فريق',

    'post_deleted_content_4' => '<br><br><br>PS: This is an automated email, please don\'t reply.',

	

	

    	// post_seller_contacted

    'post_seller_contacted_title' => 'لديك رسالة جديدة بخصوص إعلانك ":title"',

    'post_seller_contacted_title_delivey_info' => 'معلومات التوصيل بخصوص إعلانك ":title"',

    'post_seller_contacted_content_1' => '<strong>معلومات الاتصال :</strong><br>اسم: :name<br>عنوان البريد الكتروني: :email<br>رقم الهاتف: :phone<br>رسالة: :message<br><br><strong>تفضيلات التسليم:</strong> <br> :delivery_preference<br><br><strong>تفضيلات التاريخ والوقت</strong> <br> :date_time<br><br><strong>عنوان المشتري:</strong> <br> :buyer_address<br><br>تم إرسال هذا الإعلان إليك عن الإعلان ":title" لقد قدمت على<a href=":appUrl">Tammat</a> : <a href=":postUrl">:postUrl</a>',

    'post_seller_contacted_content_2' => '<br><br>الشخص الذي اتصل بك لا يعرف بريدك الإلكتروني لأنك لم تقوم بإعادة ارسال أي رسالة له',

    'post_seller_contacted_content_3' => '<br><br>تذكر دائمًا التحقق من تفاصيل شخص الاتصال (الاسم والإضافات) للتأكد من أن لديك جهة اتصال في حالة النزاع. بشكل عام ، اختر تسليم العنصر في متناول اليد<br><brحذار العروض المغرية! كن حذرًا مع الطلبات الواردة من الخارج عندما يكون لديك بريد إلكتروني للاتصال فقط. قد يكون التحويل المصرفي من قبل ويسترن يونيون أو مونيغرام الذي يتم محاكمته مصنعا.',

    'post_seller_contacted_content_4' => '<br><br>شكرا لثقتكم ونراكم قريبا',

    'post_seller_contacted_content_5' => '<br><br> Tammat فريق',

    'post_seller_contacted_content_7' => '<b><br><br>يتم تقديم خدمة التوصيل من قبل طرف ثالث حيث لا تتعامل الصفقة ولا تتحكم فيها ، ولا تتحمل أي مسؤولية Tammat عن الممارسات الموجودة على أي طرف ثالث<br><br>يرجى التحقق من نشر العنصر لمعرفة من الذي سيتم محاسبته على تكلفة التوصيل</b>',

    'post_seller_contacted_content_6' => '<br><br><br>هذا بريد إلكتروني تلقائي ، من فضلك لا ترد',





    	// user_deleted

        'user_deleted_title' => 'تم إغلاق حسابك الشخصي',

    'user_deleted_content_1' => 'مرحباً,<br><br> تم إغلاق حسابك الشخصي من <a href=":appUrl">Tammat</>بتوقيت :now.',

    'user_deleted_content_2' => '<br><br>شكراً على ثقتكم وإستخدام :appName',

    'user_deleted_content_3' => '<br><br>Tammat Team',

    'user_deleted_content_4' => '<br><br><br>PS: This is an automated email, please don\'t reply.',



    

    	// user_activated

    'user_activated_title' => 'مرحبا بك في',

    'user_activated_content_1' => 'Welcome to Tammat :userName !',

    'user_activated_content_2' => '<br>تم تفعيل حسابك بنجاح.',

    'user_activated_content_3' => '<br><br><strong>ملاحظة : Tammat فريق يقدم اليك هذه التوصيات خلال استخدامك:</strong><br><br>1 - تجنب أصحاب الإعلانات الذين يرفضون عرض صور المنتج لك بطريقة غريبة,<br>2 - تجنب قد الإمكان القيام بتحويل مبالغ مالية محلية أودولية عن طريق محلات الصرافة أو البنوك.<br><br>إذا كنت تشك أو تشتبه بأحد الاعلانات أو المستخدمين الرجاء تواصل معنا وقم بالإبلاغ ليتم ايقافه ومنعه من سرقة أو القيام بضرر أحد المستخدمين -3.',

    'user_activated_content_4' => '<br><br>You’re receiving this email because you recently created a new Tammat account. If this wasn’t you, please ignore this email.',

    'user_activated_content_5' => '<br><br>مع التحيات,<br>Tammat فريق',

	

	

	// reset_password

    'reset_password_title' => 'إعادة تعيين كلمة المرور',

    'reset_password_action' => 'إعادة تعيين كلمة المرور',

    'reset_password_content_1' => 'نسيت رقمك السري؟',

    'reset_password_content_2' => 'دعنا نساعدك في الحصول على رقم سري جديد ',

    'reset_password_content_3' => 'إذا لم تطلب إعادة تعيين كلمة المرور ، فلا يلزم اتخاذ أي إجراء آخر',

    'reset_password_content_4' => '<br><br>مع التحيات,<br>Tammat',

    'reset_password_content_5' => '<br><br>---<br>إذا كنت تواجه مشكلة في النقر على الزر "إعادة تعيين كلمة المرور" ، فقم بنسخ العنوان URL أدناه ولصقه في متصفح الويب الخاص بك<br> :link',





    // contact_form

	'contact_form_title' => 'لديك رسالة جديدة من',

    'contact_form_content' => 'Tammat - رسالة جديدة',

	

	

	// post_report_sent

    'post_report_sent_title' => 'سوء أستخدام جديد',

    'post_report_sent_content' => ' تقرير سوؤ إستخدام - Tammat/:countryCode',

    'Post URL' => 'Post URL',



	

	// post_archived

	'post_archived_title'             => 'Your Ad has been moved to your Archive',

	'post_archived_content_1'         => 'Dear :userName,<br> Your ad entitled :title and has ID No. :postNumber has been moved to your archive. You can repost your Ad from your archive without need for review or approval, unless you make a change to the post which then will be treated as a new post. Your Ad will be deleted after limited time if you do not repost it.',

	'post_archived_content_2'         => '<br><br>Signature: Thank you for using :appName <br /><br /> :appName Team',

	'post_archived_content_3'         => '<br><br>You’re receiving this email because you recently created a new ad on :appName. If this wasn’t you, please ignore this email.',

	'post_archived_content_4'         => '<br><br>Kind Regards,<br /><br /> :appName Team',





	// post_will_be_deleted

	'post_will_be_deleted_title' => 'إعلانك سوف يتم حذفه خلال :days days',

    'post_will_be_deleted_content_1' => 'مرحباً,<br><br>Your ad ":titleسوف يتم حذفه خلال :days أيام من Tammat.',

    'post_will_be_deleted_content_2' => '<br><br>يمكنك إعادة نشر إعلانك عن طريق : <a href=":repostLink">:repostLink</a>',

    'post_will_be_deleted_content_3' => '<br><br>اذا لم تتأخذ أي إجراء فسوف يتم حذفه بتاريخ :dateDel.',

    'post_will_be_deleted_content_4' => '<br><br>شكراً لثقتك ونتمى ان نراك قريباً,',

    'post_will_be_deleted_content_5' => '<br><br>Tammat فريق',

    'post_will_be_deleted_content_6' => '<br><br><br>PS: This is an automated email, please don\'t reply.',

	

	

	// post_notification

	'post_notification_title'          => 'New ad has been posted',

	'post_notification_content_1'      => 'Hello Admin,<br>The user :advertiserName has just posted a new ad.',

	'post_notification_content_2'      => '<br>The ad title: :title <br>Posted on: :now at :time',

	'post_notification_content_3'      => '<br><br>Kind Regards,<br /><br /> :appName Team',

	



	// user_notification

	'user_notification_title'        => 'New User Registration',

	'user_notification_content_1'    => 'Hello Admin,<br><br>:name has just registered.',

	'user_notification_content_2'    => '<br>Registered on: :now at :time<br>Email: <a href="mailto::email">:email</a>',

	'user_notification_content_3'    => '<br><br>Kind Regards,<br /><br /> :appName Team',

	

	

	// payment_sent

	'payment_sent_title' => 'شكراً لإتمام عملية الدفع!',

    'payment_sent_content_1' => 'مرحباً,<br><br>لقد تمت عملية الدفع بنجاح لإعلانك بعنوان ":title".',

    'payment_sent_content_2' => '<br><h1>شكراً !</h1>',

    'payment_sent_content_3' => '<br>مع أطيب التحيات,<br>Tammat Team',

	



	// payment_notification

	'payment_notification_title'     => 'New payment has been sent',

	'payment_notification_content_1' => 'Hello Admin,<br><br>The user :advertiserName ":name" has just paid a package for the ad ":title".',

	'payment_notification_content_2' => '<br><br><strong>THE PAYMENT DETAILS</strong><br><strong>Reason of the payment:</strong> Ad #:adId - :packageName<br><strong>Amount:</strong> :amount :currency<br><strong>Payment Method:</strong> :paymentMethodName',

	'payment_notification_content_3' => '<br><br>Kind Regards,<br /><br /> :appName Team',



	

	// reply_form

    'reply_form_title' => ':subject',

    'reply_form_content_1' => 'مرحباً,<br><br><strong>لقد وصلك رد من المستخدم: :senderName. إقرء الرسالة في الأسفل:</strong><br><br>',

    'reply_form_content_2' => '<br><br>مع التحيات,<br>Tammat Team',





    // send offer

    'new_offer'                   => 'عرض جديد',

    'offer_send_1' => 'مرحبا :sellername',

    'offer_send_2' => 'لقد تلقيت ردًا من: :buyername. انظر الى الرسالة أدناه:',

    'offer_send_3' => 'لقد تلقيت عرضًا جديدًا من :buyername',

    'offer_send_4' => 'المتعلقة بالإعلان:  <a href=":url">انقر هنا لترى</a>',

    'offer_send_5' => 'أطيب التحيات<br /> Tammat فريق',



    

    // offer rejected

    'offer_rejected' => 'تم رفض عرضك',

    'offer_reject_1' => 'مرحباً :toname',

    'offer_reject_2' => 'لديك عرض تم إرساله إليك من from: :fromname.أقرء الرسالة في الأسفل:',

    'offer_reject_3' => 'تم رفض عرضك المرسل الى :buyername',

    'offer_reject_4' => 'بخصوص ad: <a href=":url">Click here to see</a>',

    'offer_reject_5' => 'مع التحيات,<br />Tammat Team',

    

    

    // Expired Ad

    'expire_post' => 'تم انتهاء صلاحية إعلانك',

    'expire_post_1' => 'مرحبا :toname',

    'expire_post_2' => ' لقد انتهت صلاحية إعلانك المنشور ورالمذكور أدناه. هذا وقد تم نقل إعلانك الى قسم الأرشيف الخاص بك حيث يمكنك إعادة نشره دون الحاجة للمراجعة وانتظار الموافقة ما لم تقم باجراء أي تغيير للإعلان وذلك خلال فترة محددة والا سوف يتم حذفه من حسابك لاحقاً.',

    'expire_post_3' => 'عنوان المشاركة: :title',

    'expire_post_5' => 'https://www.tmmat.com',

    'expire_post_4' => 'أطيب التحيات,<br />فريق Tammat',

    

];

