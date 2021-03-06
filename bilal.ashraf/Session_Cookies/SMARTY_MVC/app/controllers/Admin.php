<?php

require_once '../app/models/AdminModel.php';
require_once '../lib/smarty/Smarty.class.php';
require_once '../app/models/StudentModel.php';


class Admin extends Controller
{

    public function __construct()
    {
        session_start();
    }

    public function index($key = '')
    {
        if( $key != '') {

           // unset($_COOKIE['name']);
            setcookie("name", '', time() - 3600, '/');
            unset($_SESSION['name']);
            echo "unset";
            // die("1");
        }
        $smarty = new Smarty();
        if( (!isset($_SESSION['name'])) && (!isset($_COOKIE['name'])) )
        {
            //echo "<script> alert('inside=null') </script>";
            $smarty->display('/var/www/html/bilal_workspace/SMARTY_MVC/app/views/admin/login.tpl');
        }
        else if(  (isset($_COOKIE['name'])) || isset($_SESSION['name']) )  {          //isset($_SESSION['name'])) &&
            //echo "<script> alert('inside=sessionsuccessfull') </script>";
            $this->List_All();

        }
    }

    public function Logout()
    {

        if(isset($_SESSION['name']) ||  (isset($_COOKIE['name'])))                 //&& isset($_SESSION['password'])
        {
           // echo "<script> alert('logging---out') </script>";
            setcookie("name", '', time() - 3600, '/');
            unset($_SESSION['name']);
            unset($_SESSION['password']);
            //header('Location: http://localhost/bilal_workspace/SMARTY_MVC/public/Admin/index/1');
           $smarty = new Smarty();
            $smarty->display('/var/www/html/bilal_workspace/SMARTY_MVC/app/views/admin/login.tpl');
        }
    }



    public function DeleteStd($id)
    {
        $std_model = new StudentModel();
        $std_model->from('student');
        $std_model->where("id='$id'");
        $std_model->Delete();
        header('Location: http://localhost/bilal_workspace/SMARTY_MVC/public/Admin/List_All');
        exit;
    }


    public function UpdateStd($id)
    {
        $std_model = new StudentModel();
        $data_arr1 = array(0=>'id',1=>'name', 2=>'fathername', 'dob', 'schoolname', 'city', 'destination', 'status');
        $data_sent = implode(",", $data_arr1);
        $std_model->select($data_sent, '');
        $std_model->from('student');
        $std_model->where("id='$id'");
        $result = $std_model->run_query();
        $res = mysqli_fetch_row($result);

        $smarty = new Smarty();
        $smarty->assign('name',$res[1]);
        $smarty->assign('fathername',$res[2]);
        $smarty->assign('dob',$res[3]);
        $smarty->assign('schoolname',$res[4]);
        $smarty->assign('city',$res[5]);
        $smarty->assign('destination',$res[6]);
        $smarty->assign('status',$res[7]);
        $smarty->assign('id', $id);
        $smarty->display('/var/www/html/bilal_workspace/SMARTY_MVC/app/views/admin/EditStd.tpl');
        //  $this->viewEditStd('home/Edit_Std' ,$id ,$result);
    }

    public function Update_F()
    {
        $id = $_POST['id'];
        $data_arr1 = array(0=>'name' ,'fathername' ,'dob' ,'schoolname' ,'city' ,'destination' ,'status');
        $data_arr2 = [];
        $org_data_std = [];

        for($i=0,$j=0; $i < 7; $i++) {

            $org_data_std[$j] = $data_arr1[$i] . " = '" . $_POST[$data_arr1[$i]] . "'  ";
            $j++;
        }
        $std_model = new StudentModel();
        $send_arr = implode(',' ,$org_data_std);
        $std_model->from('student');
        $std_model->Select($send_arr);
        $std_model->where("id='$id'");
        $std_model->Update();
       // header('Location: http://localhost/bilal_workspace/SMARTY_MVC/public/Student/ListAllStudents');
       // exit;
       /// $this->List_All();
    }

    public function Add_Student()
    {
        $smarty = new Smarty();
        $smarty->display('/var/www/html/bilal_workspace/SMARTY_MVC/app/views/admin/AddStd.tpl');
    }

    
    public function Add_Student_I()
    {
        $data_arr1 = array(0=>'name', 1=>'fathername', 'dob', 'schoolname', 'city', 'destination', 'status');
        $data_arr2 = [];
        $org_data_std = [];

        for($i=0,$j=0; $i<7; $i++) {
            if($_POST[$data_arr1[$i]]) {
                $org_data_std[$j] = "'" . $_POST[$data_arr1[$i]] . "'";
            }
            else {
                $org_data_std[$j] = "'" . $_POST[$data_arr1[$i]] . "'";
            }
            ++$j;
        }
        $comma_separated = implode(",", $data_arr1);
        $std_data = implode(",", $org_data_std);

        $db_criteria_obj = new Db_Criteria();
        $db_criteria_obj->select($comma_separated);
        $db_criteria_obj->from('student');
        $db_criteria_obj->Add_Std($std_data);

       // $smarty = new Smarty();
        ?> <script> alert("inserted")  </script><?php

        $smarty = new Smarty();
        $smarty->display('/var/www/html/bilal_workspace/SMARTY_MVC/app/views/admin/AddStd.tpl');


    }

    public function List_All()
    {
        $std_model = new StudentModel();
        $data_arr1 = array(0=>'id', 1=>'name', 2=>'fathername', 'dob', 'schoolname', 'city', 'destination', 'status');
        $data_sent = implode(",", $data_arr1);

        $std_model->select($data_sent, '');
        $std_model->from('student');
        //$std_model->where('id=2');

        $result = $std_model->run_query();
        $smarty = new Smarty();
        $smarty->assign('result', $result);
        // $smarty->display('../app/views/admin/index.tpl');
        $smarty->display('/var/www/html/bilal_workspace/SMARTY_MVC/app/views/admin/index.tpl');
    }
    public function Login()
    {
        //die("Script Stopped");
        $admin = new AdminModel();
        $ret = $admin->check_login($_POST['name'], md5($_POST['password']));

        if( $ret == true)
        {
            if(isset($_POST['name']) && isset($_POST['password']) ) {

                // global $_SESSION   $_COOKIE

                if( isset($_POST['remember'] )) {

                   // echo "<script> alert('cookies') </script>";
                    $cookie_name = 'name';
                    $cookie_value = $_POST['name'];

                    $cookie_name_1 = 'password';
                    $cookie_value_1 = $_POST['password'];

                    setcookie($cookie_name, $cookie_value, time() + (3600), "/");
                    //setcookie($cookie_name_1, $cookie_value_1, time() + (86400 * 30), "/");

                }


                $_SESSION['name']=$_POST['name'];
                $_SESSION['password'] = $_POST['password'];
                //header("location: profile.php");
                $std_model = new StudentModel();
                $data_arr1 = array(0=>'id', 1=>'name', 2=>'fathername', 'dob', 'schoolname', 'city', 'destination', 'status');
                $data_sent = implode(",", $data_arr1);

                $std_model->select($data_sent, '');
                $std_model->from('student');
                //$std_model->where('id=2');

                $result = $std_model->run_query();
                $smarty = new Smarty();
                $smarty->assign('result', $result);
                // $smarty->display('../app/views/admin/index.tpl');
               //echo "<script> alert('inside=login') </script>";
                $smarty->display('/var/www/html/bilal_workspace/SMARTY_MVC/app/views/admin/index.tpl');

            }

        }
        else if($ret == false) {
            echo "<script> alert('WRONG INPUT!!!!')  </script>";
            $smarty = new Smarty();
            $smarty->display('/var/www/html/bilal_workspace/SMARTY_MVC/app/views/admin/login.tpl');
            //die("stop");
            //sleep(10);
          // header('Location: http://localhost/bilal_workspace/SMARTY_MVC/public/Admin/index');
           // exit;
        }

    }
}
