<?php

function getAcc($userid) {
    $user = \App\Models\User::where("userid", $userid)->first();
    $username = $user->username ?? "";
    if ($username == "") {
      return "";
    }
    return substr($username,0, 3)."******";
}


function getChar($userid) {
  $user = \App\Models\User::where("userid", $userid)->first();
  $username = $user->username ?? "";
  if ($username) {
    return $user->getMain();
  } else {
    return "Chưa tạo nhân vật";
  }
}

function getName($char) {
  $char = \App\Models\Char::where("char_id", $char)->first();
  return $char ? $char->getName() : "Chưa cập nhật";
}