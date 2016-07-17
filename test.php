<?php
use PirloDB\Database;

require_once 'vendor/autoload.php';

$test = new Database();
$test->setTable('users');

$users = $test->select()->all();
// $users = $test->select('username, password')->all();
// $users = $test->select('username')->first();
// $users = $test->where('username', '=', 'endy')->first();
// $users = $test->select('username')->where('username', '=', 'hilman')->all();
// $users = $test->where('username', '=', 'hilman')->orWhere('username', '=', 'endy')->all();
// $users = $test->where('username', '=', 'hilman')->where('password', '=', 12312312)->all();

// $test->create([
//   'username' => 'mantapski',
//   'password' => 'zombieski',
// ]);

// $test->where('username', '=', 'jango')->update([
//   'password' => 'passyydo',
//   'username' => 'jango',
// ]);

// $test->where('username', '=', 'namabaru')->orWhere('password', '=', 'zobieys2')->update([
//   'password' => 'passbarubitz',
// ]);

// $test->where('username', '=', 'zmanbr"os')->delete();

// $users = $test->select()->orderBy('username', 'DESC')->all();

// $users = $test->select()->where('username', 'LIKE', '%a%')
//               ->orderBy('username', 'DESC')->take(3)->all();

var_dump($users);
