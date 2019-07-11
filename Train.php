<?php

// Training Variables
$desired_error = 0.0003;
$max_epochs = 500000;
$current_epoch = 0;
$epochs_between_saves = 5; // Minimum number of epochs between saves
$epochs_since_last_save = 0;

// Training Data
$data1 = dirname(__FILE__) . "/Fallout_Bot_HealthBar_Training_Data_1_Convolutions.data";
$data2 = dirname(__FILE__) . "/Fallout_Bot_HealthBar_Training_Data_2_Convolutions.data";

// Initialize pseudo mse (mean squared error) to a number greater than the desired_error
// this is what the network is trying to minimize.
$pseudo_mse_result = $desired_error * 10000; // 1
$best_mse = $pseudo_mse_result; // keep the last best seen MSE network score here

// Initialize ANN
$num_input = 612;
$num_output = 2;
$num_neurons_hidden_1 = 8;
$num_neurons_hidden_2 = 6;
$layers = array($num_input, $num_neurons_hidden_1, $num_neurons_hidden_2, $num_output);
$num_layers=count($layers);

// Create ANN
$ann = fann_create_standard_array ( $num_layers , $layers);

if ($ann) {
  echo 'Training ANN... ' . PHP_EOL;
 
  // Configure the ANN
  fann_set_training_algorithm ($ann , FANN_TRAIN_RPROP);
  fann_set_activation_function_hidden($ann, FANN_SIGMOID_SYMMETRIC);
  fann_set_activation_function_output($ann, FANN_SIGMOID_SYMMETRIC);
 
  // Read training data
  $train_data_1 = fann_read_train_from_file($data1);
  $train_data_2 = fann_read_train_from_file($data2);
  
  // Merge into new data resource
  $train_data = fann_merge_train_data ($train_data_1, $train_data_2);

  // Remove the partial data resources from memory
  fann_destroy_train ( $train_data_1 );
  fann_destroy_train ( $train_data_2 );
 
 
  // Check if pseudo_mse_result is greater than our desired_error
  // if so keep training so long as we are also under max_epochs
  while(($pseudo_mse_result > $desired_error) && ($current_epoch <= $max_epochs)){
  $current_epoch++;
  $epochs_since_last_save++; 
 
  // See: http://php.net/manual/en/function.fann-train-epoch.php
  // Train one epoch
  //
  // One epoch is where all of the training data is considered
  // exactly once.
  //
  // This function returns the MSE error as it is calculated
  // either before or during the actual training. This is not the
  // actual MSE after the training epoch, but since calculating this
  // will require to go through the entire training set once more.
  // It is more than adequate to use this value during training.
  $pseudo_mse_result = fann_train_epoch ($ann , $train_data );
  echo 'Epoch ' . $current_epoch . ' : ' . $pseudo_mse_result . PHP_EOL; // report
   
  // If we haven't saved the ANN in a while...
  // and the current network is better then the previous best network
  // as defined by the current MSE being less than the last best MSE
  // Save it!
  if(($epochs_since_last_save >= $epochs_between_saves) && ($pseudo_mse_result < $best_mse)){
   
    $best_mse = $pseudo_mse_result; // we have a new best_mse
   
    // Save a Snapshot of the ANN
    fann_save($ann, dirname(__FILE__) . "/train.net");
    echo 'Saved ANN.' . PHP_EOL; // report the save
    $epochs_since_last_save = 0; // reset the count
  }
 
  } // While we're training

  echo 'Training Complete! Saving Final Network.'  . PHP_EOL;
 
  // Save the final network
  fann_save($ann, dirname(__FILE__) . "/finished.net"); 
  fann_destroy($ann); // free memory
}
echo 'All Done!' . PHP_EOL;
