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
 * New products related  class
 *
 * @package   		Core_CNewProducts
 * @category    	Core
 * @author    		AJ Square Inc Dev Team
 * @link   		http://www.zeuscart.com
  * @copyright 	        Copyright (c) 2008 - 2013, AJ Square, Inc.
 * @version   		Version 4.0
 */
class Core_CNewProducts
{

	/**
	 * Stores the output
	 *
	 * @var array 
	 */	
	var $output = array();	
	/**
	 * Stores the output
	 *
	 * @var array 
	 */	
	var $arr = array();
	/**
	 * This function is used to show  the  product in home page
	 * 
	 * 
	 * @return HTML data
	 */	
	function newProducts()
	{
		
		 $sql= " SELECT a.product_id, a.title, a.thumb_image,a.image,a.product_status,a.category_id ,a.msrp,a.sub_category_id ,a.sub_under_category_id, a.intro_date,b.soh,sum(c.rating)/count(c.user_id) as 			rating	FROM products_table a INNER JOIN	product_inventory_table b ON a.product_id=b.product_id  left join product_reviews_table c on a.product_id=c.product_id 
		WHERE a.intro_date <= '".date('Y-m-d')."' and a.status=1 group by a.product_id ORDER BY rand( ) LIMIT 0,12 ";
			
		$query = new Bin_Query();
		if($query->executeQuery($sql))
		{
			$j=0;
			$cnt=count($query->records);
			if($cnt>0)
			{
			
				for ($i=0;$i<$cnt;$i++)
				{		
					foreach($query->records as $row)
					{
						$r[$j]=$row;
						$prid=$row['product_id'];
						$minval=Core_CNewProducts::disRates($prid);
						if($minval > 0  or $minval!='')
						{
							
							$r[$j]['msrp']= $_SESSION['currencysetting']['selected_currency_settings']['currency_tocken'].number_format($row['msrp']*$_SESSION['currencysetting']['selected_currency_settings']['conversion_rate'],2).' - '.$_SESSION['currencysetting']['selected_currency_settings']['currency_tocken'].number_format($minval*$_SESSION['currencysetting']['selected_currency_settings']['conversion_rate'],2);
						}
						else
							$r[$j]['msrp']= $_SESSION['currencysetting']['selected_currency_settings']['currency_tocken'].number_format($row['msrp']*$_SESSION['currencysetting']['selected_currency_settings']['conversion_rate'],2);
							
						$j++;
					}
					return  Display_DNewProducts::newProducts($query->records,$r);
				}
			}
		}
		else
		{
			return Display_DNewProducts::newProductsElse();
		}
		
	}
	/**
	 * This function is used to show  the rating 
	 * @param integer $productid
	 * 
	 * @return HTML data
	 */	
	function disRates($productid)
	{
		$sql='select min(msrp) as msrp from msrp_by_quantity_table where product_id ='.$productid;
		$obj=new Bin_Query();
		$obj->executeQuery($sql);
		$val=$obj->records;
		return $val[0]['msrp'];
	}
	/**
	 * This function is used to show  the produtcs
	 * 
	 * 
	 * @return HTML data
	 */	
	function viewProducts()
	{

		$pagesize=9;
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

		if(($_GET['cat']!='') && ($_GET['subcat']=='') && ($_GET['subundercat']==''))
		{
			$sql="SELECT * from category_table where category_name ='".$_GET['cat']."'";
			$obj=new Bin_Query();
			$obj->executeQuery($sql);
			$records=$obj->records;
			$objpro=new Bin_Query();

			//product selection
			$sqlpro="SELECT * FROM products_table WHERE category_id='".$records[0]['category_id']."' OR sub_category_id='".$records[0]['category_parent_id']."' OR 	sub_under_category_id='".$records[0]['sub_category_parent_id']."'";

		}
		elseif(($_GET['subcat']!='') && ($_GET['cat']!='') && ($_GET['subundercat']==''))
		{
 			$sql="SELECT * FROM  category_table WHERE category_parent_id IN(SELECT category_id 
			from category_table where category_name ='".$_GET['cat']."') AND category_name='".$_GET['subcat']."'"; 

			$obj=new Bin_Query();
			$obj->executeQuery($sql);
			$records=$obj->records;
			$objpro=new Bin_Query();

			//product selection
		 	$sqlpro="SELECT * FROM products_table WHERE category_id='".
			$records[0]['category_parent_id']."' AND sub_category_id='".$records[0]['category_id']."' ";

		}
		elseif(($_GET['subundercat']!='')&&( $_GET['subcat']!='') && ($_GET['cat']!=''))
		{	
			 $sql="SELECT * FROM  category_table WHERE category_parent_id IN(SELECT category_id 
			from category_table where category_name ='".$_GET['cat']."') AND category_id  IN (SELECT category_id from category_table where category_name ='".$_GET['subundercat']."')"; 
			
			$obj=new Bin_Query();
			$obj->executeQuery($sql);
			$records=$obj->records;
			$objpro=new Bin_Query();

			//product selection
		 	$sqlpro="SELECT * FROM products_table WHERE category_id='".
			$records[0]['category_parent_id']."' AND sub_category_id='".$records[0]['sub_category_parent_id']."' AND 	sub_under_category_id='".$records[0]['category_id']."'  ";
		}


		$objpro=new Bin_Query();
		if($objpro->executeQuery($sqlpro))
		{	

			  $sql1=$sqlpro.' LIMIT '.$start.','.$end;
			$total = ceil($objpro->totrows/ $pagesize);
			$recordSet=$objpro->records;
			include('classes/Lib/Paging.php');
			$tmp = new Lib_Paging('classic',array('totalpages'=>$total, 'length'=>5),'pagination');
			$this->data['paging'] = $tmp->output;
			$this->data['prev'] =$tmp->prev;
			$this->data['next'] = $tmp->next;
			$query1 = new Bin_Query();
				$query1->executeQuery($sql1);	
		}
		
			
		
		return Display_DNewProducts::viewProducts($query1->records,$this->data['paging'],$this->data['prev'],$this->data['next'],$start);
		
	

	}

}
?>