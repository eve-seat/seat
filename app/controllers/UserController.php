<?php

class UserController extends BaseController {

  /*
  |--------------------------------------------------------------------------
  | __construct()
  |--------------------------------------------------------------------------
  |
  | Sets up the class to ensure that CSRF tokens are validated on the POST
  | verb
  |
  */

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

  /*
  |--------------------------------------------------------------------------
  | getAll()
  |--------------------------------------------------------------------------
  |
  | Get all of the users in the database
  |
  */

  public function getAll()
  {
    $users = Sentry::findAllUsers();

    return View::make('user.all')
      ->with(array('users' => $users));
  }

  /*
  |--------------------------------------------------------------------------
  | getNewUser()
  |--------------------------------------------------------------------------
  |
  | Return a view to add new users
  |
  */

  public function getNewUser()
  {
    return View::make('user.new');  
  }

  /*
  |--------------------------------------------------------------------------
  | postNewUser()
  |--------------------------------------------------------------------------
  |
  | Registers a new user in the database
  |
  */

  public function postNewUser()
  {

    if ($user = Sentry::register(array(
      'email'       => Input::get('email'),
      'password'    => Input::get('password'),
      'first_name'  => Input::get('first_name'),
      'last_name'  => Input::get('last_name'),
    ), true)) {
      if (Input::get('is_admin') == 'yes') {
        try {
          $adminGroup = Sentry::findGroupByName('Administrators');
        }
        catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
          return Redirect::back()
            ->withInput()
            ->withErrors('Administrators group could not be found');
        }
        $user->addGroup($adminGroup);
      }

      return Redirect::action('UserController@getDetail', array($user->getKey()))
        ->with('success', 'User ' . Input::get('email') . ' has been added');
    }
    else
      return Redirect::back()
        ->withInput()
        ->withErrors('Error adding user');
  }

  /*
  |--------------------------------------------------------------------------
  | getDetail()
  |--------------------------------------------------------------------------
  |
  | Show all of the user details
  |
  */
  
  public function getDetail($userID)
  {

    try {
      $user = Sentry::findUserById($userID);
    }
    catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
      App::abort(404);
    }

    return View::make('user.detail')
      ->with('user', $user);
  }

  /*
  |--------------------------------------------------------------------------
  | postUpdateUser()
  |--------------------------------------------------------------------------
  |
  | Changes a user's details
  |
  */

  public function postUpdateUser()
  {

    try {
      $user = Sentry::findUserById(Input::get('userID'));
    }
    catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
      App::abort(404);
    }

    try {
      $adminGroup = Sentry::findGroupByName('Administrators');
    }
    catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
      return Redirect::back()
        ->withInput()
        ->withErrors('Administrators group could not be found');
    }

    $user->email = Input::get('email');
    if (Input::get('password') != '')
      $user->password = Input::get('password');
    $user->first_name = Input::get('first_name');
    $user->last_name = Input::get('last_name');

    if (Input::get('is_admin') == 'yes') {
      $user->addGroup($adminGroup);
    }
    else {
      $user->removeGroup($adminGroup);
    }

    if ($user->save())
      return Redirect::action('UserController@getDetail', array($user->getKey()))
        ->with('success', 'User has been updated');
    else
      return Redirect::back()
        ->withInput()
        ->withErrors('Error updating user');
  }

  /*
  |--------------------------------------------------------------------------
  | getDeleteUser()
  |--------------------------------------------------------------------------
  |
  | Deletes a user from the database
  |
  */

  public function getDeleteUser($userID)
  {

    try {
      $user = Sentry::findUserById($userID);
      $user->delete();
    }
    catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
      App::abort(404);
    }

    return Redirect::action('UserController@getAll')
      ->with('success', 'User has been deleted');
  }

}