<?php 
/**
* GNU General Public License.

* This file is part of ZeusCart V4.

* ZeusCart V4 is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 4 of the License, or
* (at your option) any later version.
* 
* ZeusCart V4 is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with Foobar. If not, see <http://www.gnu.org/licenses/>.
*
*/

/**
 * This class contains functions to displays the subadmins available in the database
 *
 * @package  		Core_CSubAdminManagement
 * @category  		Core
 * @author    		AjSquareInc Dev Team
 * @link   		http://www.zeuscart.com
   * @copyright 		Copyright (c) 2008 - 2013, AjSquare, Inc.
 * @version  		Version 4.0
 */


 class Core_CSubAdminManagement
{
 	
	/**
	 * Function gets and displays the sub admin details from the database
	 * @param array $Err contains both error messages and error values
	 * 
	 * @return string
	 */
	function displaySubAdmin($Err)
	{
	   $pagesize=10;
	   if(isset($_GET['page']))
		{
		    
			$start = trim($_GET['page']-1) *  $pagesize;
			$end =  $pagesize;
		}
		else 
		{
			$start = 0;
			$end =  $pagesize;
		}
		$total = 0;
		$sql='select * from subadmin_table';
		$obj=new Bin_Query();
  	    $obj->executeQuery($sql);
		//{
			$total = ceil($obj->totrows/ $pagesize);
			include('classes/Lib/Paging.php');
			$tmp = new Lib_Paging('classic',array('totalpages'=>$total, 'length'=>10),'pagination');
			$this->data['paging'] = $tmp->output;
			$this->data['prev'] =$tmp->prev;
			$this->data['next'] = $tmp->next;
		$sql = "select * from subadmin_table LIMIT $start,$end";
		$query = new Bin_Query();
		$query->executeQuery($sql);
			return Display_DSubAdminManagement::displaySubAdmin($query->records,$Err,$this->data['paging'],$this->data['prev'],$this->data['next']);
		/*}
		else
		{
			return "No News Found";
		}*/
		//return Display_DSubAdminManagement::displaySubAdmin($obj->records,$Err); 
	}
	

	/**
	 * Function display the details for the selected sub admin
	 * 
	 * 
	 * @return string
	 */
	
		
	function displaySelectedSubAdmin()
	{
		 $id=$_GET['id'];
		 $sql='select * from subadmin_table where subadmin_id='.$id;
         $obj1=new Bin_Query();
		 $obj1->executeQuery($sql);
		 return Display_DSubAdminManagement::displaySelectedSubAdmin($obj1->records); 
	}
	
	
	/**
	 * Function updates the changes for the selected sub admin  
	 * 
	 * 
	 * @return string
	 */
	
	function updateSubAdmin()
	{

	     //  $name= $row[0]['subadmin_name'];
   		 //  $password= $row[0]['subadmin_password'];
  		 //  $email= $row[0]['subadmin_email_id']; 
   		  // $status= $row[0]['subadmin_status']; 
  		$id= $_POST['id'];
		$name=$_POST['subadminname'];
	    $status=$_POST['subadminstatus'];
		$email=$_POST['subadminemail'];
		$password=$_POST['subadminpassword'];
		if($status)
		{
		    $status=1;
		}
		else
		{
		    $status=0;
		}
  //$sql="update subadmin_table set subadmin_name='".$name."',subadmin_password='".base64_encode($password)."',subadmin_email_id='".$email."', subadmin_status=".$status." where subadmin_id=".$id;
  $sql="update subadmin_table set subadmin_password='".base64_encode($password)."',subadmin_email_id='".$email."', subadmin_status=".$status." where subadmin_id=".$id;
 
   	  $obj=new Bin_Query();
		$obj->updateQuery($sql); 
	}
	
	/**
	 * Function deletes a selected sub admin from the database
	 * 
	 * 
	 * @return string
	 */
	
	
	function deleteSubAdmin()
	{
	     $id=$_GET['id'];
		 $sql='delete from subadmin_table where subadmin_id='.$id;
         $obj1=new Bin_Query();
		 $obj1->updateQuery($sql);
		 $sql='delete from subadmin_roles_table where subadmin_id ='.$id;
         $obj1=new Bin_Query();
		 $obj1->updateQuery($sql);
	}
	
	/**
	 * Function adds a new sub admin to the database
	 * 
	 * 
	 * @return string
	 */
	
	function insertSubAdmin()
	{
	   $name=  $_POST['subadminname'];
  	   $password= $_POST['subadminpassword'];
	   $email=$_POST['subadminemail'];
	   $status=$_POST['subadminstatus'];
	   if($status)
	   {
	      $status=1;
	   }
	   else
	   {
	      $status=0;
	   }
	 //  if($name!='' && $email!='' && $password!='')
	   //{
		    $sql="insert into subadmin_table (subadmin_name,subadmin_password,subadmin_email_id,subadmin_status)values('".$name."','".base64_encode($password)."','".$email."',".$status.")";
		    $obj=new Bin_Query();
  	   		$obj->updateQuery($sql);
	   //}
	
	}
	
	/**
	 * Function checks whether the sub admin exists.
	 * 
	 * 
	 * @return bool
	 */
	
	function checkSubadminExists()
	{
		$name=  $_POST['subadminname'];
		
		$sql="select count(*) as cnt from subadmin_table where subadmin_name='".$name."'";
		$obj1=new Bin_Query();
		$obj1->executeQuery($sql);
		
		print_r($obj1->records);
		if ($obj1->records[0]['cnt'] > 0 )
			return true;
		else
			return false;
	}
}
?>