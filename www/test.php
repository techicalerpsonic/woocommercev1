<?php
	

	//Include API
	// require_once( "woocommerce/includes/class-wc-api.php" );
	// require_once( "woothemes/woocommerce-api/lib/woocommerce-api.php");

	//Include Order
	// require_once( "woothemes/woocommerce-api/lib/woocommerce-api/exceptions/class-wc-api-client-exception.php" );
	// require_once( "woothemes/woocommerce-api/lib/woocommerce-api/resources/class-wc-api-client-resource-orders.php" );
	// require_once( "woothemes/woocommerce-api/lib/woocommerce-api/exceptions/class-wc-api-client-exception.php" );
	// require_once( "woothemes/woocommerce-api/lib/woocommerce-api/exceptions/class-wc-api-client-exception.php" );

	// class Data{

		// if (is_ajax()) {
		  // if (isset($_GET["action"]) && !empty($_GET["action"])) { //Checks if action value exists
		  //   $action = $_GET["action"];
		  //   if($action == "test")
		  //   {
		  //   	test();
		  //   }
		  // }
		// }

		// function is_ajax() {
		// 	var_dump(isset($_SERVER['HTTP_X_REQUESTED_WITH']));
		// 	var_dump(isset($header['X-Requested-With']));
		//   return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
		// }
		// function testa()
		// {
		//     var_dump($_GET["action"]);
		// 	echo "GOT";
		// }
		
		// function test()
		// {
			// echo "GOTCHA !";
			$options = array(
			    'ssl_verify'      => false,
			);

			try {
				//Read & Write Account
				$consumer_key = 'ck_ac8a4d2b03e1edeaaf7d524eb91998b4a4a7fba5';
				$consumer_secret = 'cs_a672a4199ce540f95da38bea88fd0ecb0f4230e8';

				//Read-only Account
				// $consumer_key = 'ck_a4ad61f95ea60296ac4947ffd064f7fed0b5956e';
				// $consumer_secret = 'cs_91b90d5b3139a9d6be26ad0ae7dacca773ec605c';

			    // $client = new WC_API_Client( 'http://revo.webmurahbagus.com', $consumer_key, $consumer_secret, $options );

			   //  if (isset($_GET["action"]) && !empty($_GET["action"])) { //Checks if action value exists
				  //   $action = $_GET["action"];
				  //   if($action == "test")
				  //   {
			   //  		getProductData($client);
				  //   }
				  //   else if($action == "test1")
				  //   {
			   //  		getAllCategories($client);
				  //   }
				  //   else if($action == "single_produk" && $_GET["slug"] != '')
				  //   {
				  //   	getProductByCategories($client, $_GET["slug"]);
				  //   }
			  	// }	
			    sendEmail();

			} catch ( WC_API_Client_Exception $e ) {
			    echo $e->getMessage() . PHP_EOL;
			    echo $e->getCode() . PHP_EOL;

			    if ( $e instanceof WC_API_Client_HTTP_Exception ) {

			        print_r( $e->get_request() );
			        print_r( $e->get_response() );
			    }
			}
		// }

		function sendEmail()
		{
			$to = "aagusta@studen.ciputra.ac.id";
			$subject = "My subject";
			$txt = "Hello world!";
			$headers = "From: josal.kevin@gmail.com" . "\r\n";

			mail($to,$subject,$txt,$headers);
		}

		function getProductData($client)
		{
			$json = array();
			$arr = array();
			$temp = array();	
			$obj = $client->products->get();
		    $products = $obj->products;
		    $counter = 0;

		    foreach ($products as $key) {
			    $json["produk".$counter] = 
			        array(
		    			"nama_produk" 				=> $key ->title,
		    			"harga"						=> $key ->price,
		    			"range_harga"				=> $key ->price_html,
		    			"deskripsi"					=> $key ->short_description,
		    			"kategori_produk"			=> $key ->categories,
		    			"gambar_produk" 			=> $key ->images,
			        );

			    if(count($key ->attributes) != 0)
			    {
			    	for($i = 0; $i < count($key ->attributes); $i++)
			    	{
			    		$attribute = $key ->attributes;
			    		
				    	for($j = 0; $j < count($key ->variations); $j++)
				    	{
				    		$variation = $key ->variations;
		    				if(strcasecmp($attribute[$i]->name, $variation[$j]->attributes[$i]->name) == 0)
		    				{
					    		$arr[$j] = array(
				    				// "nama_vari	asi" 		=> $variation[$j]->attributes[0]->name,
			    					"pilihan_variasi" 	=> $variation[$j]->attributes[$i]->option,
			    					"harga_variasi" 	=> $variation[$j]->price,
			    				);
		    				}
				    	}
						$json["produk".$counter][$attribute[$i]->name] = $arr;

						array_push($temp, $attribute[$i]->name);
			    	}
			    	$json["produk".$counter]["atribut"] = $temp;
			    }
			    $counter++;
				$temp = array();	
		    }

			echo json_encode($json);
		}
		function getAllCategories($client)
		{
			$json = array();
			$json["menu_categories"] = array();	

			$obj = $client->products->get_categories();
			$categories = $obj->product_categories;	

			$counter = 0;

			foreach ($categories as $key) {
				if($key->parent == 0)
				{
					$json["root_categories".$counter] = 
				        array(
			    			"id" 					=> $key ->id,
			    			"name" 					=> $key ->name,
			    			"slug" 					=> $key ->slug,
				        );
					array_push($json["menu_categories"], $json["root_categories".$counter]);
					unset($json["root_categories".$counter]);
			    	$counter++;
				}
			}

			$arr = array();
			$temp = array();
			$temp2 = array();
			$counter = 0;

			for($i=0; $i < count($json["menu_categories"]);$i++)
			{
				$category = $json["menu_categories"][$i];
				// $a = $category["id"];

				foreach ($categories as $cat) {
					if($category["id"] == $cat->parent)
					{
						$temp = 
							array(
				    			"id" 				=> $cat ->id,
				    			"name" 				=> $cat ->name,
				    			"slug" 				=> $cat ->slug,
					        );

						array_push($temp2, $temp);
						$json["menu_categories"][$i]["sub_categories"] = $temp2;
					}
				}
				$temp2 = array();
			}

			echo json_encode($json);
		}
		// function getCategoriesByParent($client,$id_parent,$list_parent)
		// {
		// 	$json = array();
		// 	$temp = array();
		// 	$temp2 = array();

		// 	$obj = $client->products->get_categories();
		// 	$categories = $obj->product_categories;

		// 	$json["menu_categories"] = $list_parent;

		// 	foreach ($categories as $cat) {
		// 		if($id_parent == $cat->parent)
		// 		{
		// 			$temp = 
		// 				array(
		// 	    			"id" 				=> $cat ->id,
		// 	    			"name" 				=> $cat ->name,
		// 	    			"slug" 				=> $cat ->slug,
		// 		        );

		// 			array_push($temp2, $temp);
		// 			$json["menu_categories"][$id_parent]["sub_categories"] = $temp2;
		// 		}
		// 	}
		// 	echo json_encode($json);
		// }
		function getProductByCategories($client, $slug)
		{
			$counter 	= 0;
			$json = array();
			$arr = array();
			$temp = array();
			$json["produk"] = array();
			$obj 		= $client->products->get(null,array('filter[category]'=> $slug));
			$categories = $obj->products;

			foreach ($categories as $key) {
			    $json["produk".$counter] = 
			        array(
		    			"nama_produk" 				=> $key ->title,
		    			"harga"						=> $key ->price,
		    			"range_harga"				=> $key ->price_html,
		    			"deskripsi"					=> $key ->short_description,
		    			"kategori_produk"			=> $key ->categories,
		    			"gambar_produk" 			=> $key ->images,
			        );

			    if(count($key ->attributes) != 0)
			    {
			    	for($i = 0; $i < count($key ->attributes); $i++)
			    	{
			    		$attribute = $key ->attributes;
			    		
				    	for($j = 0; $j < count($key ->variations); $j++)
				    	{
				    		$variation = $key ->variations;
		    				if(strcasecmp($attribute[$i]->name, $variation[$j]->attributes[$i]->name) == 0)
		    				{
					    		$arr[$j] = array(
				    				// "nama_vari	asi" 		=> $variation[$j]->attributes[0]->name,
			    					"pilihan_variasi" 	=> $variation[$j]->attributes[$i]->option,
			    					"harga_variasi" 	=> $variation[$j]->price,
			    				);
		    				}
				    	}
						$json["produk".$counter][$attribute[$i]->name] = $arr;

						array_push($temp, $attribute[$i]->name);
			    	}
			    	$json["produk".$counter]["atribut"] = $temp;
			    	array_push($json["produk"], $json["produk".$counter]);
			    	unset($json["produk".$counter]);
			    }
			    $counter++;
				$temp = array();	
		    }

			echo json_encode($json);
		}
		function getOrder($client)
		{
			$obj = $client->orders->get();
		}
		function createOrder($client)
		{
			$data = array(
				'order' => array(
					'status' 						=> "on-hold",
					// 'payment_details'				=> '',
					// 'shipping_tax' 					=> '0.50',
					// 'shipping_methods'				=> 'Flat Rate',
					'billing_address' 				=> [
						'first_name'				=> 'Lagi',
						'last_name'					=> 'Magang',
						'company'					=> '',
						'address_1'					=> 'Apartemen ciputra',
						'address_2'					=> 'None',
						'city'						=> 'Surabaya',
						'state'						=> 'JI',
						'postcode'					=> '60217',
						'country'					=> 'ID',
						'email'						=> 'tester@gmail.com',
						'phone'						=> '0123456789'
						],
					'shipping_address' 				=> [
						'first_name'				=> 'Lagi',
						'last_name'					=> 'Magang',
						'company'					=> '',
						'address_1'					=> 'Apartemen ciputra',
						'address_2'					=> 'None',
						'city'						=> 'Surabaya',
						'state'						=> 'JI',
						'postcode'					=> '60217',
						'country'					=> 'ID',
						'email'						=> 'tester@gmail.com',
						'phone'						=> '0123456789'
						],
					'line_items' => [
				            [
				                'product_id' => 360,
				                'quantity' => 3,
				            ]
			        	],
					'shipping_lines' => [
				            [
				                'method_id' => 'flat_rate',
				                'method_title' => 'Flat Rate',
				                'total' => 2
				            ]
				        ]
			        )
					// 'line_items' 					=> array(
					// 	// 'order_id'					=> '007',
					// 	'product_id'				=> 225,
					// 	// 'sku'						=> '1',
					// 	'name'						=> 'Product A',
					// 	'subtotal'					=> '10000000',
					// 	'subtotal_tax'				=> '0',
					// 	'total'						=> '10000000',
					// 	// 'total_tax'					=> '0',
					// 	// 'price'						=> '999',
					// 	'quantity'					=> '2'
					// 	// 'tax_class'					=> '',
					// 	)
				);

			$order = new WC_API_Client_Resource_Orders($client);

			$order->create($data);
			var_dump($order);
		}
	// }



?>

