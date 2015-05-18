<?php

class Ajax_Controller_Forms  extends K_Controller_Ajax {

    public function contactusAction() {
        $this->isAjaxErr();
        $this->isPostErr();

        $dictionary = array(

            'name' => 'Ваше имя',
            'lname' => 'Ваша фамилия',
            'phone' => 'Ваш телефон',
            'email' => 'E-mail',
            'mess' => 'Текст письма'

        );

        $data = array(

            'name' => substr(htmlspecialchars(trim($_POST['name'])), 0, 125),
            'lname' => substr(htmlspecialchars(trim($_POST['lname'])), 0, 30),
            'phone' => substr(htmlspecialchars(trim($_POST['phone'])), 0, 30),
            'email' => substr(htmlspecialchars(trim($_POST['email'])), 0, 50),
            'mess' => substr(htmlspecialchars(trim($_POST['mess'])), 0, 10000)

        );

        $validate = array(

            'name' =>  array('required' => true),
            'phone' => array('required' => true),
            'mess' => array('required' => true)

        );

        if(!empty($data['lname'])){
            $validate['lname'] = array('required' => true);
        }

        if(!empty($data['email'])){
            $validate['email'] = array('required' => true,
                'max'=>255,
                'email'
            );
        }

        $model = new Admin_Model_Valid;

        if (!$model->isValidRow($data, $validate)) {

            $returnJson['error'] = true;
            $returnJson['errormsg'] = $model->getErrorsD($dictionary);

            $this->putJson($returnJson);

        }

        $mess = ' <b>Имя отправителя:</b>'.$data['name'].'<br />
                  <b>Фамилия отправителя:</b>'.$data['lname'].'<br />
                  <b>Контактный телефон:</b>'.$data['phone'].'<br />
                  <b>Контактный email:</b>'.$data['email'].'<br />
                  <b>Сообшение:</b>'.$data['mess'].'<br /> ';


        // подключаем файл класса для отправки почты
        require APP_PATH.'/plugins/class.phpmailer.php';

        $mail = new PHPMailer();
        $mail->From = 'noreply@odesstroy.od.ua'; // от кого
        $mail->FromName = 'Одесстрой форма обратной связи'; // от кого
        $mail->AddAddress("user@gmail.com", 'Premier'); // кому - адрес, Имя -- premier-vip@ua.fm
        $mail->IsHTML(true); // выставляем формат письма HTML
        $mail->Subject =  "Одесстрой обратная связь";  // тема письма

        $mail->Body = $mess;

        // отправляем наше письмо

        if (!$mail->Send()) {

            $jsonReturn['error'] = true;
            $jsonReturn['msg'] = "Ошибка отправки почты";


        }else{

            $jsonReturn['error'] = false;
            $jsonReturn['msg'] = "Сообщение успешно добавленно";
          //  $jsonReturn['clean'] = true;

        }

        $this->putJSON($jsonReturn);
    }

}