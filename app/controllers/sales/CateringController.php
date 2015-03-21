<?php

class CateringController  extends BaseController {

	public function getCatering(){

		$input = Input::all();
		// echo '<pre>'; print_r($package_array); echo '</pre>'; exit;


		$cData = Catering::with(array('Images' => function($query)
			{
				$query->where('section', '=', 'CATERING')->orderBy(DB::raw('RAND()'))->where('active', '=', 1);
			}))
		->orderBy('name','ASC')->where('active', '=', 1)->get();




		foreach ($cData as $package) {
			// echo '<pre>'; print_r($npackage); echo '</pre>'; exit;

			$count = count($package->Images);
			if($count < 1){
				$package_image[$package->id] = 'ingredient.png';
			}else{
				foreach($package->Images as $image){
			        if(file_exists('uploads/'.$image->name)){
			            $package_image[$package->id] = $image->name;
			        }else{
			           	$package_image[$package->id] = 'ingredient.png';
			        }
				}
			}
		}
		// echo '<pre>'; print_r($cData); echo '</pre>'; exit;


		
		$recipes = MenuRecipes::orderBy('name','ASC')->where('active', '=', '1')->get();

		$mRep = array();
		$mRep[0]	= '- Select Recipe -';	
		foreach ($recipes as $recipe) {
			$mRep[$recipe->id]	= $recipe->name;
		};
		

		return View::make('public.catering')->with(array(
			'cData' => $cData,
			'catering_image' => $package_image,
			'recipes' => $mRep,
			)
		);
	}

	public function getCreatePackage(){
		$input = Input::all();
		// echo '<pre>'; print_r($input); echo '</pre>';exit;


		//dd($input);
		$rules = array(
			'amount' 		=> 'required'
		);
		
		$validator = Validator::make($input, $rules);
		
		if($validator->fails()){
			return Redirect::back()
				->withErrors($validator);
		}
			//$mCatUp = MenuCategories::findOrFail('id');
		else{
			
			if(isset($input['recipes']) && isset($input['amount'])){			
				$catering_recipes = Input::get('recipes');
				$input_amount = Input::get('amount');
				$last_price = 0;
				
				for($i=0; $i<count($catering_recipes); $i++){
					$catering_recipe_pivot_id = array_keys($catering_recipes[$i]);
				    $catering_recipe_pivot_id = $catering_recipe_pivot_id[0];
				    $catering_recipe_id = $catering_recipes[$i][$catering_recipe_pivot_id];

				    $amount_array = array_keys($input_amount[$i]);
				    $index_amount = $amount_array[0];
				    $amount = $input_amount[$i][$index_amount];
				    
				   
				    $rData = SalesData::where('menu_recipe_id', '=', $catering_recipe_id)->get();
				    // $rData = SalesData::where('menu_recipes_id','=', $catering_recipe_id);

				    $total_price = $rData[0]->sales_price * $amount;
				    // echo '<pre>'; print_r($total_price); echo '</pre>';exit;

					$package_array[] = $arrayName = array(
						'recipe_id' => $catering_recipe_id, 
						'amount' => $amount, 
						'price' => $rData[0]->sales_price, 
						'total_price' => $total_price, 
					);

					$last_price = $last_price + $total_price;	

					
				};
			};



			if(isset($input['ddi'])){
				$ddi = $input['ddi'];

				for($i=0; $i<count($ddi); $i++){
					foreach ($package_array as $key => $value) {
						if($ddi[$i] == $key){
							$last_price = $last_price - $package_array[$key]['total_price'];
							unset($package_array[$ddi[$i]]);
							
						}
					}

				}
				
				
			};
		}; 

		// foreach ($package_array as $key => $value) {
		// 	echo '<pre>'; print_r($value['amount']); echo '</pre>';
		// }							
		// exit;

		$cData = Catering::with(array('Images' => function($query)
			{
				$query->where('section', '=', 'CATERING')->orderBy(DB::raw('RAND()'))->where('active', '=', 1);
			}))
		->orderBy('name','ASC')->where('active', '=', 1)->get();
		foreach ($cData as $package) {
			$count = count($package->Images);
			if($count < 1){
				$package_image[$package->id] = 'ingredient.png';
			}else{
				foreach($package->Images as $image){
			        if(file_exists('uploads/'.$image->name)){
			            $package_image[$package->id] = $image->name;
			        }else{
			           	$package_image[$package->id] = 'ingredient.png';
			        }
				}
			}
		}
		$recipes = MenuRecipes::orderBy('name','ASC')->where('active', '=', '1')->get();
		$mRep = array();
		$mRep[0]	= '- Select Recipe -';	
		foreach ($recipes as $recipe) {
			$mRep[$recipe->id]	= $recipe->name;
		};

		if(Auth::user()){
			$user = Auth::user();
		}else{
			$user = 0;
		}

			// echo '<pre>'; print_r($user->email); echo '</pre>'; exit;

		if(isset($input['cancel'])){
			return View::make('public.catering')->with(array(
				'cData' => $cData,
				'catering_image' => $package_image,
				'recipes' => $mRep,
				'active' => 'active'
				)
			);
		}

		return View::make('public.catering')->with(array(
			'cData' => $cData,
			'catering_image' => $package_image,
			'recipes' => $mRep,
			'package_array' => $package_array,
			'last_price' => $last_price,
			'active' => 'active',
			'user' => 'user'
			)
		);
	}

	public function getCustomCatering($package_array){

		
		echo '<pre>'; print_r($package_array); echo '</pre>'; exit;


		
	}

	public function getPackage($id)
	{
		$pData = Catering::where('active', '=', '1')->where('id', '=', $id)
			->with(array('menuRecipes' => function($query) use ($id){
				
				$query->where('catering_recipes.catering_id', '=', $id)->orderBy('pivot_ordering','ASC')
				->with(array('Images' => function($query){
					$query->where('ordering', '=', 0)->where('section', '=', 'RECIPE');
				}));
			}))			
		->get();

		if(Auth::user()){
			$user = Auth::user();
		}else{
			$user = 0;
		}

		foreach($pData as $package){
			foreach ($package->MenuRecipes as $recipe) {

				$iData = Images::where('active', '=', '1')
					->where('link_id', '=', $recipe->id)
					->where('ordering', '=', 0)
					->where('section', '=', 'RECIPE')
					->get();
				
				// echo '<pre>'; print_r($iData[0]->name); echo '</pre>';exit;	
				
				$i_count = count($iData);
				if($i_count > 0){
					$recipe_image[$recipe->id] = $iData[0]->name;
				}else{
					$recipe_image[$recipe->id] = 'recipe.png';
				}	
				
			}
		}
		// echo '<pre>'; print_r($recipe_image); echo '</pre>';exit;	
			
			
		// foreach($pData as $package){
		// 	echo '<pre>'; print_r($package->id); echo '</pre>';
		// }exit;

		return View::make('sales.package')->with(array(
			'pData' => $pData,
			'recipe_image' => $recipe_image,
			'user' => $user,
			)
		);
	}



	public function packageEnquiry(){

		$input = Input::all();

		$fname = $input['fname'];
		$date = $input['date'];
		$time = $input['time'];
		$email = $input['email'];
		$d_message = $input['message'];

		// $user = Auth::user();
		// 	echo '<pre>'; print_r($user); echo '</pre>';exit;
		
		// $messages = array(
		//     'size'    => 'Your :attribute must be :size numbers long.',
		// );
		$rules = array(
			'fname' => 'required',
			'date' => 'required',
			'time' => 'required',
			'email' => 'required|email',
			'mobile' => 'required|numeric',
			'message' => 'required',
		);
		$validator = Validator::make($input, $rules);
		
		if($validator->fails()){
			return Redirect::back()
				->withErrors($validator)
				->withInput($input);
		}else{

			

			
			if(Auth::user()){
				$user = Auth::user();
				// echo '<pre>'; print_r($user); echo '</pre>';exit;
			}else{
				$user_id = 0;
			}
			if(isset($input['package_id'])){
				$package_id = $input['package_id'];

				$cData = Catering::with(array('Images' => function($query)
					{
						$query->where('section', '=', 'CATERING')->orderBy(DB::raw('RAND()'))->where('active', '=', 1);
					}))
				->orderBy('name','ASC')->where('active', '=', 1)->get();

				$pData = Catering::where('active', '=', '1')->where('id', '=', $package_id)
					->with(array('menuRecipes' => function($query) use ($package_id){
						
						$query->where('catering_recipes.catering_id', '=', $package_id)->orderBy('pivot_ordering','ASC')
						->with(array('Images' => function($query) use ($package_id){
							$query->where('ordering', '=', 0)->where('section', '=', 'RECIPE');
						}));
					}))
					->with(array('Images' => function($query){
						$query->where('section', '=', 'CATERING')->orderBy(DB::raw('RAND()'))->where('active', '=', 1);
					}))		
				->get();
			

				foreach ($pData as $package) {
				// echo '<pre>'; print_r($npackage); echo '</pre>'; exit;

					$count = count($pData[0]->Images);
					if($count < 1){
						$package_image[$package->id] = 'ingredient.png';
					}else{
						foreach($package->Images as $image){
					        if(file_exists('uploads/'.$image->name)){
					            $package_image[$package->id] = $image->name;
					        }else{
					           	$package_image[$package->id] = 'ingredient.png';
					        }
						}
					}

					foreach ($package->MenuRecipes as $recipe) {

						$iData = Images::where('active', '=', '1')
							->where('link_id', '=', $recipe->id)
							->where('ordering', '=', 0)
							->where('section', '=', 'RECIPE')
							->get();
						
						// echo '<pre>'; print_r($iData[0]->name); echo '</pre>';exit;	
						
						$i_count = count($iData);
						if($i_count > 0){
							$recipe_image[$recipe->id] = $iData[0]->name;
						}else{
							$recipe_image[$recipe->id] = 'recipe.png';
						}	
						
					}
				}

				$messageData = array(
			        'pData' => $pData,
					'package_image' => $package_image,
					'fname' => $fname,
					'date' => $date,
					'time' => $time,
					'email' => $email,
					'd_message' => $d_message,
					'recipe_image' => $recipe_image,
			    );

				Mail::send('sales.package_email', $messageData, function($message) use ($email, $pData){
					$message->to( $email )->cc('sales@sonaughtybutnice.com')->subject('Confirmation, We recieved your catering enquiry - '.$pData[0]->name);
				}); //->cc('sales@sonaughtybutnice.com')
			}else{
				$messageData = array(
					'fname' => $fname,
					'date' => $date,
					'time' => $time,
					'email' => $email,
					'd_message' => $d_message,
			    );

				Mail::send('sales.catering_email', $messageData, function($message) use ($email){
					$message->to( $email )->cc('sales@sonaughtybutnice.com')->subject('Catering Confirmation, We recieved your catering enquiry!');
				}); //->cc('sales@sonaughtybutnice.com')
			}

		    return Redirect::action('CateringController@getPackage', array($package_id))
			->with('message', 'We have just sent you a confirmation email.<br/>
					If you do not recieve an email please check the email you typed in =) </br>
					We will contact you as soon as possible!
			');

		    
		    // return Redirect::to('package/'$package_id);
			
		}
	}
}



	

























		// $rData = MenuRecipes::orderBy(DB::raw('RAND()'))->where('active', '=', 1)->get();
		// $r_count = 0;
		// foreach($rData as $data){
		// 	$r_object = MenuRecipes::findOrFail($data->id);
		// 	$rData[$r_count]['image'] = $r_object->Images()->where('ordering', '=', 0)->take(1)->get();
		// 	$r_count++;
		// };//echo '<pre>'; print_r($rData); echo '</pre>';exit;
		
		// $rData = MenuCategories::orderBy(DB::raw('RAND()'))->where('menu_categories.active', '=', 1)
		// ->with(array('menuRecipes' => function($query)
		// {
		// 	//while(mysqli_num_rows(MenuCategories) > 0){
		// 	$query->where('menu_recipes.active', '=', 1)->where('ordering', '=', 0)
		// 		->with(array('Images' => function($query)
		// 		{
		// 			$query->where('ordering', '=', 0);
		// 		}));
		// 	//}
		// }))->get();
		
		
		
		
		// $rData = MenuRecipes::orderBy('created_at','DESC')->where('active', '=', 1)
		// 		->with(array('MenuCategories' => function($query)
		// 		{
		// 			$query->where('menu_categories.active', '=', 1);
		// 		}))
				
		// 		->with(array('Images' => function($query){
		// 			$query->where('images.ordering', '=', 0);
		// 		}))
			
		// 	->get();
		// //echo '<pre>'; print_r($rData); echo '</pre>';exit;