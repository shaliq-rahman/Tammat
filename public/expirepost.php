<?php


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.dealnotdeal.com/expire-post-cron");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$data = curl_exec($ch);



//     require_once('PHPMailer/class.phpmailer.php');
// 	define('DB_SERVER', "localhost");
// 	define('DB_USER', "dealnotd_dealnotdeal");
// 	define('DB_PASS', '$ZuD0@Rz#B+{');
// 	define('DB_DATABASE', "dealnotd_dealnotdeal");
// 	$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

    
//     $getpost = mysqli_query($con, 'SELECT * FROM `posts` WHERE archived = 0 ');
//     while($row = mysqli_fetch_assoc($getpost))
//     {
        
        
//       $getuser = mysqli_query($con, 'SELECT * FROM `users` where id = '.$row['user_id'].'  ');
//       $rowuser = mysqli_fetch_assoc($getuser);
        
// $body= '<table class="" style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;width:100%;margin:0;padding:20px" bgcolor="#f6f6f6">
//   <tbody>
//       <tr style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;margin:0;padding:0">
//          <td style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;margin:0;padding:0"></td>
//          <td class="" style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;clear:both!important;display:block!important;max-width:800px!important;Margin:0 auto;padding:20px;border:1px solid #f0f0f0" bgcolor="#FFFFFF">
//             <div class="" style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;display:block;max-width:800px;margin:0 auto;padding:0">
//               <table style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;width:100%;margin:0;padding:0">
//                   <tbody>
//                      <tr style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;margin:0;padding:0">
//                         <td style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;margin:0;padding:0">
//                           <p style="font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:1.6em;font-weight:normal;margin:0 0 10px;padding:0;display:inline-block;max-width:100%;word-wrap:break-word">
//                               Hello '.$rowuser['username'].',<br><br><strong>Your post has expired.</strong><br><br>
//                                 Post Title : '.$row['title'].'
//                               <br><br>Kind Regards,<br>The Deal Not Deal Team
//                           </p>
//                         </td>
//                      </tr>
//                   </tbody>
//               </table>
//             </div>
//          </td>
//          <td style="font-family:Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;margin:0;padding:0"></td>
//       </tr>
//   </tbody>
// </table>';

       
            
//             $checkpayment = mysqli_query($con,"SELECT payments.*,packages.duration FROM `payments`  INNER JOIN packages ON  packages.id = payments.package_id  WHERE payments.post_id = '".$row['id']."'  ");
//             if(mysqli_num_rows($checkpayment) == '0')
//             {
//                 $getduration =  mysqli_query($con,"SELECT * FROM `packages` WHERE id = 1 ");
//                 $rowduration = mysqli_fetch_assoc($getduration);
//                 $duration = $rowduration['duration'];
                
//                 $date = $row['created_at'];
//                 $sevendate = date('Y-m-d', strtotime($date. ' + '.$duration.' days'));
//                 if($sevendate < date('Y-m-d'))
//                  {
//                     mysqli_query($con,"UPDATE posts SET archived = 1  WHERE id = '".$row['id']."'  ");
                    
//                     $mail  = new PHPMailer(); 
//                     $address = $rowuser['email'];
//                     $mail->AddReplyTo("admin@dealnotdeal.com","Deal Not Deal");
//                     $mail->SetFrom('admin@dealnotdeal.com', 'Deal Not Deal');
//                     $mail->AddReplyTo("admin@dealnotdeal.com","replay");
//                     $mail->AddAddress($address, "Deal Not Deal");
//                     $mail->Subject    = "Expire Post";
//                     $mail->MsgHTML($body);
//                     $mail->Send();
                   
//                  }   
//             }
//             else
//             {
//                 $rowduration = mysqli_fetch_assoc($checkpayment);
//                 $duration = $rowduration['duration'];
//                 $date = $row['created_at'];
//                 $sevendate = date('Y-m-d', strtotime($date. ' + '.$duration.' days'));
//                 if($sevendate < date('Y-m-d'))
//                 {
//                     mysqli_query($con,"UPDATE posts SET archived = 1  WHERE id = '".$row['id']."'  ");
                    
//                     $mail  = new PHPMailer(); 
//                     $address = $rowuser['email'];
//                     $mail->AddReplyTo("admin@dealnotdeal.com","Deal Not Deal");
//                     $mail->SetFrom('admin@dealnotdeal.com', 'Deal Not Deal');
//                     $mail->AddReplyTo("admin@dealnotdeal.com","replay");
//                     $mail->AddAddress($address, "Deal Not Deal");
//                     $mail->Subject    = "Expire Post";
//                     $mail->MsgHTML($body);
//                     $mail->Send();
    
//                 }   
//             }
        
        
        
        

//     }

?>