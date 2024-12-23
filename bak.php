<?php

session_start();

include 'db.php';

$login_fail = false;


// form 컨트롤 php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // form에 form-name 값이 온지 확인
    if (isset($_POST['form-name'])) {

        // login form 일경우
        if ($_POST['form-name'] == 'login-form') {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);

            // Prepared Statement를 사용하여 SQL 인젝션 방지
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->bind_param("ss", $username, $password); // "s" 는 추가할 문자열의 갯수
            $stmt->execute();

            // 결과 가져오기(Select 일때만)
            $result_login = $stmt->get_result();


            // $sql_login = "SELECT * FROM users WHERE username='$username' AND password='$password'";
            // $result_login = $conn->query($sql_login);

            if ($result_login->num_rows > 0) {

                $sql_userdata = "SELECT u_point,email,nickname,user_role,u_id FROM users WHERE username='$username'";
                $result_userdata = $conn->query($sql_userdata);

                $u_point = "";
                $email = "";
                $nickname = "";

                //변수에 값 저장
                if ($result_userdata->num_rows > 0) {

                    while ($row = $result_userdata->fetch_assoc()) {
                        $u_point = $row['u_point'];
                        $email = $row['email'];
                        $nickname = $row['nickname'];
                        $user_role = $row['user_role'];
                        $id = $row['u_id'];
                    }
                }

                // 세션에 정보 저장
                $_SESSION['user_access'] = true;
                $_SESSION['userid'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['u_point'] = $u_point;
                // $_SESSION['email'] = $email; 
                $_SESSION['nickname'] = $nickname;
                $_SESSION['user_role'] = $user_role;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {

                $call_alert_modal = [true, "로그인 실패", "ID 나 PASSWORD가 틀립니다.", false];
            }


            // 화원가입 form 일 경우
        } elseif ($_POST['form-name'] == 'signin-form') {

            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);
            $nickname = htmlspecialchars($_POST['nickname']);

            // Prepared Statement를 사용하여 SQL 인젝션 방지
            $stmt = $conn->prepare("INSERT INTO users values(null,?,?,?,0,'test@test.com','user')");
            $stmt->bind_param("sss", $nickname, $username, $password); // "s" 는 추가할 문자열의 갯수
            $stmt->execute();

            if ($conn->affected_rows > 0) {

                $sql_userdata = "SELECT u_id FROM users WHERE username='$username'";
                $result_userdata = $conn->query($sql_userdata);
                $id = "";

                //변수에 값 저장
                if ($result_userdata->num_rows > 0) {

                    while ($row = $result_userdata->fetch_assoc()) {
                        $id = $row['u_id'];
                    }
                }


                // 세션에 정보 저장
                $_SESSION['user_access'] = true;
                $_SESSION['userid'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['u_point'] = 0;
                // $_SESSION['email'] = $email; 
                $_SESSION['nickname'] = $nickname;
                $_SESSION['user_role'] = 'user';
                // $_SESSION['signin_success'] = true;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {

                $call_alert_modal = [true, "회원가입 실패", "관리자에게 문의 하십시요.", false];
            }
        } elseif ($_POST['form-name'] == 'sc-form') {
            $test_set = $_POST['sc-isweb'];

            if (isset($_POST['sc-isweb'])) {

                $p_title = $_POST['sc-title'];
                $p_web = true;
                $p_link = $_POST['sc-link'];
                $p_point = $_POST['sc-point'];
                $p_difficulty = "";
                $p_key = $_POST['sc-key'];

                if ($_POST['sc-difficulty'] == 1) {

                    $p_difficulty = "Normal";
                } elseif ($_POST['sc-difficulty'] == 2) {

                    $p_difficulty = "Hard";
                } else {

                    $p_difficulty = "Easy";
                }


                $stmt = $conn->prepare("INSERT INTO solution_data VALUES(null,?,null,?,?,null,?,?,?,null)");
                $stmt->bind_param("sisiss", $p_title, $p_web, $p_link, $p_point, $p_difficulty, $p_key);
                $stmt->execute();


                header("Location: " . $_SERVER['PHP_SELF']); // 같은 페이지로 리다이렉트
                exit;
            } else {
            }
        } else {
        }
    }
}





?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- google icon -->


    <style>
        /* 색 조정 */
        :root {
            --neon-green: #00ff41;
            --neon-pink: #ff2a6d;
            --neon-blue: #05d9e8;
            --dark: #0d0208;
            --matrix-bg: #001000;
        }

        .password-check-msg {
            display: none;
        }

        #chat-box {
            display: none;
        }

        .main-menu-list {
            transition: all 0.3s ease;
        }

        /* .container-fluid {
            padding-left: 0;
            padding-right: 0;
        } */

        .navbar-brand {
            font-size: 24px;

        }

        .neon {
            color: var(--neon-green) !important;
            text-transform: uppercase;
            letter-spacing: 3px;
            animation: flicker 3s infinite;
        }


        /* 깜빡임 애니메이션 */
        @keyframes flicker {

            0%,
            19.999%,
            22%,
            62.999%,
            64%,
            64.999%,
            70%,
            100% {
                opacity: 1;
                text-shadow: 0 0 10px var(--neon-green),
                    0 0 20px var(--neon-green),
                    0 0 40px var(--neon-green);
            }

            20%,
            21.999%,
            63%,
            63.999%,
            65%,
            69.999% {
                opacity: 0.4;
                text-shadow: none;
            }
        }
    </style>
</head>





<body class="" style="background-color: #2a2a2a ;">



    <nav class="navbar navbar-light bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand text-white neon" href="#">
                <img src="/docs/5.0/assets/brand/bootstrap-logo.svg" alt="" width="30" height="24"
                    class="d-inline-block align-text-top">
                TEAM LED
            </a>

            <div>
                <div class="d-flex">
                    <span class="me-4 pt-1" id="n-name"></span>
                    <span class="me-4 pt-1" id="n-point"></span>
                    <button type="button" class="btn btn-primary me-4" id="n-ch-btn">채팅 <span class="badge bg-secondary">4</span></button>
                    <button type="button" class="btn btn-primary me-5" id="n-logout-btn">로그아웃</button>
                </div>




                <!-- 로그인 모달 -->
                <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content bg-light">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalToggleLabel">로그인</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="login-form" method="POST" action="">
                                    <input type="hidden" name="form-name" value="login-form">
                                    <div class="mb-3">
                                        <label class="form-label" for="username-login">Id </label>
                                        <input class="form-control" id="username-login" type="text" name="username">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="password-login">Password </label>
                                        <input class="form-control" id="password-login" type="password" name="password">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="login-btn" name="submit">로그인</button>
                                <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">회원가입</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 회원가입 모달 -->
                <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content bg-light">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalToggleLabel2">회원가입</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="signin-form" method="POST" action="">
                                    <input type="hidden" name="form-name" value="signin-form">
                                    <div class="mb-3">
                                        <label class="form-label" for="username">Id </label>
                                        <div class="d-flex">
                                            <input class="form-control col" id="username" type="text" name="username">
                                            <button class="btn btn-success col-3" type="button" id="id-ch-btn">중복 확인</button>
                                        </div>
                                        <span id="id-check-msg"></span>

                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="nickname">nickname</label>
                                        <input class="form-control" id="nickname" type="text" name="nickname">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="password">Password </label>
                                        <input class="form-control" id="password" type="password" name="password">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="password-check">Password check</label>
                                        <input class="form-control" id="password-check" type="password" name="password-check">
                                        <span class="password-check-msg"></span>
                                    </div>

                                </form>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="signin-btn" name="submit">회원가입</button>
                                <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">돌아가기</button>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="btn btn-primary me-5" data-bs-toggle="modal" href="#exampleModalToggle" role="button" id="n-login">로그인</a>


                <!-- 범용 alert 모달 -->
                <div class="modal fade" id="alert-modal" tabindex="-1" aria-labelledby="alert-modal-title" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="alert-modal-title"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="alert-modal-body">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="alert-modal-end" data-bs-dismiss="modal">닫기</button>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

            <!-- <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form> -->
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">


            <div class="col-1 main-menu-list ps-0">
                <nav>

                    <div class="list-group c-menu-list" id="list-tab" role="tablist">
                        <div class="bg-secondary text-white d-flex justify-content-center p-2">MENU</div>
                        <a class="list-group-item list-group-item-action active list-group-item-secondary d-flex justify-content-center"
                            id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab"
                            aria-controls="list-home"><span class="material-symbols-outlined">home</span></a>
                        <a class="list-group-item list-group-item-action list-group-item-secondary d-flex justify-content-center" id="list-profile-list"
                            data-bs-toggle="list" href="#list-profile" role="tab" aria-controls="list-profile"><span class="material-symbols-outlined">edit_note</span></a>
                        <a class="list-group-item list-group-item-action list-group-item-secondary d-flex justify-content-center" id="list-messages-list"
                            data-bs-toggle="list" href="#list-messages" role="tab"
                            aria-controls="list-messages"><span class="material-symbols-outlined">sports_score</span></a>
                        <a class="list-group-item list-group-item-action list-group-item-secondary d-flex justify-content-center" id="list-settings-list"
                            data-bs-toggle="list" href="#list-settings" role="tab"
                            aria-controls="list-settings"><span class="material-symbols-outlined">finance</span></a>
                        <div class="admin_m bg-secondary text-white d-flex justify-content-center p-2 mt-2">Admin MENU</div>
                        <a class="admin_m list-group-item list-group-item-action list-group-item-secondary d-flex justify-content-center"
                            id="list-user-list" data-bs-toggle="list" href="#list-user" role="tab"
                            aria-controls="list-user"><span class="material-symbols-outlined">home</span></a>
                        <a class="admin_m list-group-item list-group-item-action list-group-item-secondary d-flex justify-content-center" id="list-item-list"
                            data-bs-toggle="list" href="#list-item" role="tab" aria-controls="list-item"><span class="material-symbols-outlined">edit_note</span></a>

                    </div>

                </nav>
            </div>



            <div class="col-10" id="body main" style="background-color: #3d3d3d ; color: #e0e0e0;">

                <div>

                    <div class="row">
                        <div class="col pt-3" style="min-height: 100vh;">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="list-home" role="tabpanel"
                                    aria-labelledby="list-home-list">

                                    <div class="col">환영 합니다.</br>

                                        ⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⠀⠀⠀⠀⠀⣀⣤⡶⠶⠟⠛⠛⠛⠋⠙⠛⠛⠿⢶⣦⣄⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀ </br>
                                        ⠀⠀⠀⠀⣴⡾⠋⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⠙⢿⣦⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⠀⢠⣾⠏⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣀⣀⣀⣀⣽⣿⣆⠀⠀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⢠⣿⠃⠀⠀⢰⡶⠾⠿⠿⠛⠛⠻⣿⠋⠀⠀⢸⡟⠉⠉⣭⣍⢹⡿⣷⡀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⣾⠃⠀⠀⠀⣿⡀⠀⠀⠰⠿⠆⣠⡿⠀⠀⠀⠈⢷⣤⣀⣼⡿⠟⠀⠹⣷⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⢸⡟⠀⠀⠀⠀⠘⠿⣶⣤⣤⣶⠾⠟⠁⠀⠀⠀⠀⠀⠈⠉⣁⣀⣀⠀⠀⢻⡇⠀⠀⠀⠀⠀⠀</br>
                                        ⢸⡇⠀⠀⠀⠀⢀⣀⣠⣤⣤⣤⡶⠶⠶⠶⠶⠖⠛⠛⠛⠛⣿⠋⠉⠀⠀⢸⣿⠀⠀⠀⠀⠀⠀</br>
                                        ⣺⡇⠀⠀⠀⠈⠉⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣸⡇⠀⠀⠀⣼⡇⠀⠀⠀⣤⡄⠀</br>
                                        ⠸⣷⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣶⠀⢠⡿⠁⠀⣠⣾⠏⠀⠀⠀⢀⣿⣇⠀</br>
                                        ⠀⠹⣿⣄⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣰⣿⣦⠟⠁⣠⣾⠟⠁⠀⠀⠀⠀⣿⠉⣽⠂</br>
                                        ⠀⠀⠈⠻⢷⣦⣄⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣼⠋⣹⣿⣴⡿⠋⠀⠀⢀⣠⣤⣶⣿⡽⠞⠁⠀</br>
                                        ⠀⠀⠀⠀⠀⣸⡿⠻⠿⢶⣶⣶⣶⣶⣶⠶⣛⣷⡾⠛⠉⣿⣁⣠⠴⢞⣫⡵⠟⠋⠁⠀⠀⠀⠀</br>
                                        ⠀⠀⠀⠀⣰⡟⠀⠀⢀⣤⡴⠟⣋⣥⡶⠚⠋⠁⠀⠀⠀⣿⣋⣤⠶⠛⠉⠀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⠀⠀⢰⡿⠀⠀⠐⣋⣤⣶⠟⠋⠁⠀⠀⠀⠀⠀⠀⠀⣿⠋⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⠀⢠⣿⠃⠀⠀⠘⠛⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⠀⣼⡟⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⢠⣿⠁⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢸⣿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀</br>
                                        ⠀⣼⡟⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠘⣿⠀⠀</br>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                                    <div class="container-fluid px-4"> <!-- container를 container-fluid로 변경하고 패딩 추가 -->
                                        <div class="row justify-content-start g-2" id="s-list">
                                            <!-- 문제 박스들이 여기에 추가됨 -->
                                        </div>
                                    </div>


                                </div>
                                <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">




                                </div>
                                <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">

                                    진행도
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                    </div>


                                </div>
                                <div class="tab-pane fade" id="list-user" role="tabpanel" aria-labelledby="list-user-list">

                                    user


                                </div>
                                <div class="tab-pane fade" id="list-item" role="tabpanel" aria-labelledby="list-item-list">
                                    <div class="d-flex justify-content-center align-items-center ps-5 pe-5 pt-2 pb-0">
                                        <span class="fs-3 pb-4 fw-bold">문제 추가</span>
                                    </div>
                                    <div class="container border border-secondary p-5 bg-white">

                                        <form id="sc-form" method="POST" action="">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sc_isweb" name="sc-isweb">
                                                <label class="form-check-label" for="sc_isweb">WEB?</label>
                                            </div>


                                            <label class="form-label" for="sc-title">문제 제목</label>
                                            <input class="form-control" id="sc-title" type="text" name="sc-title">

                                            <label class="form-label" for="sc-ssh">ssh 링크</label>
                                            <input class="form-control" id="sc-ssh" type="text" name="sc-ssh">

                                            <label class="form-label" for="sc-link">web 링크</label>
                                            <input class="form-control" id="sc-link" type="text" name="sc-link">

                                            <label class="form-label" for="sc-key">키 값</label>
                                            <input class="form-control" id="sc-key" type="text" name="sc-key">

                                            <label for="sc-point" class="form-label">점수 : </label>&nbsp;<span id="sc-p-v" class="fw-semibold"></span>
                                            <input type="range" class="form-range" min="5" max="15" id="sc-point" name="sc-point">

                                            <label for="sp-difficulty" class="form-label">난이도 : </label>&nbsp;<span id="sc-d-v" class="fw-semibold"></span>
                                            <input type="range" class="form-range" min="0" max="2" id="sc-difficulty" name="sc-difficulty">

                                            <label class="form-label" for="sc-hint">설명</label>
                                            <textarea class="form-control" id="sc-hint" type="text" name="sc-hint" rows="5"></textarea>

                                            <label class="form-label" for="sc-content">힌트</label>
                                            <textarea class="form-control" id="sc-content" type="text" name="sc-content" rows="3"></textarea>

                                            <input type="hidden" name="form-name" value="sc-form">

                                            <div class="d-flex justify-content-center align-items-center p-5 pb-0">
                                                <button class="btn btn-primary ">추가 하기</button>
                                            </div>




                                        </form>

                                    </div>



                                </div>
                            </div>
                        </div>


                        <!-- <div class="h-auto col-2 border border-2 mt-3 ms-3 h-auto" id="chat-box">


            </div> -->
                    </div>



                </div>

            </div>

            <div class="col-1"></div>

        </div>

    </div>











    <script>
        const n_name_el = document.getElementById('n-name');
        const n_point_el = document.getElementById('n-point');
        const n_login_el = document.getElementById('n-login');
        const n_ch_btn_el = document.getElementById('n-ch-btn');
        const n_logout_btn_el = document.getElementById('n-logout-btn');
        let is_id_check = false;
        let is_pass_check = false;


        const s_list_el = document.getElementById('s-list');
        setSolution = () => {

            fetch('getsolution.php') // PHP 파일 경로로 변경
                .then(response => response.json())
                .then(data => {
                    console.log(data); // 받아온 데이터 출력

                    data.forEach(item => {

                        var s_box = document.createElement('div');
                        // col-2로 변경하여 6개씩 배치되도록 설정 (12/2=6)
                        s_box.className = "col-2 mb-2 mx-1 pb-3 plist bg-dark text-white border border-3 border-secondary";
                        // 최소/최대 너비 설정으로 박스 크기 제어
                        s_box.style.minWidth = "220px";
                        s_box.style.maxWidth = "245px";
                        
        

                        var s_title_box = document.createElement('div');
                        s_title_box.className = "d-flex justify-content-center border-top border-2 border-danger";
                        s_title_box.style.cursor = "pointer";
                        s_title_box.addEventListener('click', () => {

                            window.location.href = item.s_link;


                        })

                        var s_title = document.createElement('span');
                        s_title.textContent = item.s_title;
                        s_title.className = "fw-bold fs-5";

                        var s_content_box = document.createElement('div');

                        var s_label = document.createElement('label');
                        s_label.className = "form-label";
                        s_label.setAttribute("for", "sol" + item.s_id);
                        s_label.innerHTML = `<span class="fs-6">난이도: ${item.s_difficulty}</span>
                                   <span class="fs-6">점수: ${item.s_point}</span>`;

                        var s_input = document.createElement('input');
                        s_input.className = "form-control ptext";
                        s_input.id = "sol" + item.s_id;

                        if (item.s_clear == 'true') {


                            s_box.style.backgroundColor = '#4CAF50';
                            s_input.style.backgroundColor = '#4CAF50';
                            s_input.value = 'CLEAR!';
                            s_input.type = 'text';
                            s_input.readOnly = true;



                        } else {


                            s_input.setAttribute('placeholder', "key input");
                            s_input.setAttribute('type', "password");
                            s_input.addEventListener('input', function() {

                                if (s_input.value === item.s_key) {
                                    console.log('통과')
                                    s_box.style.backgroundColor = '#4CAF50';
                                    s_input.style.backgroundColor = '#4CAF50';
                                    s_input.value = 'CLEAR!';
                                    s_input.type = 'text';
                                    s_input.readOnly = true;

                                    s_title_box.classList.replace('border-danger', 'border-success');
                                    // s_box.classList.replace('border-secondary', 'border-success');


                                    var xhr = new XMLHttpRequest();
                                    xhr.open('POST', 'clearset.php', true);
                                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                                    xhr.onload = function() {

                                        if (xhr.status === 200) {

                                            let response = xhr.responseText;
                                            console.log(response);

                                        }

                                    };
                                    const jsonData = JSON.stringify({

                                        username: "<?php echo $_SESSION['username']; ?>",
                                        sid: item.s_id

                                    });

                                    xhr.send(jsonData);




                                } else {
                                    console.log('ㄴㄴ')

                                }

                            });



                        }



                        s_box.appendChild(s_title_box);
                        s_box.appendChild(s_content_box);
                        s_title_box.appendChild(s_title);
                        s_content_box.appendChild(s_label);
                        s_content_box.appendChild(s_input);
                        s_list_el.appendChild(s_box);




                        // const plist_el = document.querySelector('.plist');
                        // const ptext_el = document.querySelector('.ptext');
                        // ptext_el.addEventListener('input', function() {



                        // });

                    });

                })
                .catch(error => console.error('Error:', error));







        }

        const init = () => {

            setSolution();
            set_sc_form();
            getpointview();

            let userAccess = "<?php echo $_SESSION['user_access']; ?>";
            console.log('userAccess=' + userAccess);
            if (userAccess == true) {
                console.log('로그인됨');

                n_name_el.textContent = "<?php echo $_SESSION['nickname']; ?>"
                n_point_el.textContent = "포인트 : " + "<?php echo $_SESSION['u_point']; ?>"

                n_name_el.style.display = 'block';
                n_point_el.style.display = 'block';
                n_ch_btn_el.style.display = 'block';
                n_login_el.style.display = 'none';
                n_logout_btn_el.style.display = 'block';



            } else {

                n_name_el.style.display = 'none';
                n_point_el.style.display = 'none';
                n_ch_btn_el.style.display = 'none';
                n_login_el.style.display = 'block';
                n_logout_btn_el.style.display = 'none';

            }



        }




        let chaton = false;
        // const mid_body_el = document.querySelector('.mid-body');


        const chat_box_el = document.getElementById('chat-box');
        n_ch_btn_el.addEventListener('click', function() {
            if (chaton === false) {

                // mid_body_el.className = 'mid-body col-7 pt-3';
                // chat_box_el.style.display = 'block'
                // chaton = true;

            } else {

                // mid_body_el.className = 'mid-body col-9 pt-3';
                // chat_box_el.style.display = 'none'
                // chaton = false;

            }



        });

        const password_el = document.getElementById('password');
        const password_check_el = document.getElementById('password-check');
        const password_check_msg_el = document.querySelector('.password-check-msg');
        const is_pass_check_ok = () => {

            if (password_check_el.value == password_el.value) {
                password_check_msg_el.textContent = '암호가 일치합니다.';
                password_check_msg_el.style.color = 'green';
                password_check_msg_el.style.display = 'block';
                is_pass_check = true;
            } else {
                password_check_msg_el.textContent = '암호가 일치하지 않습니다.';
                password_check_msg_el.style.color = 'red';
                password_check_msg_el.style.display = 'block';
                is_pass_check = false;
            }

        }

        password_check_el.addEventListener('input', function() {

            is_pass_check_ok();

        });

        password_el.addEventListener('input', function() {

            is_pass_check_ok();

        });


        document.getElementById('login-btn').addEventListener('click', function() {

            document.getElementById('login-form').submit();

        });

        n_logout_btn_el.addEventListener('click', function() {

            window.location.href = 'logout.php';


        });

        // 중복체크 ==============================================================
        const id_check_msg_el = document.getElementById('id-check-msg');
        const is_id_check_ok = (data) => {


            if (data == 'true') {
                id_check_msg_el.style.color = 'green';
                id_check_msg_el.textContent = '사용 가능한 Id 입니다.';
                is_id_check = true;

            } else if (data == 'null') {

                id_check_msg_el.style.color = 'red';
                id_check_msg_el.textContent = 'Id를 입력하세요.';
                is_id_check = false;


            } else {

                id_check_msg_el.style.color = 'red';
                id_check_msg_el.textContent = '이미 있는 Id 입니다.';
                is_id_check = false;

            }

        }

        document.getElementById('signin-btn').addEventListener('click', function() {

            if (is_pass_check == true) {

                if (is_id_check == true) {

                    document.getElementById('signin-form').submit();

                } else {

                    id_check_msg_el.style.color = 'red';
                    id_check_msg_el.textContent = '중복체크 하셔야합니다!.';

                }

            } else {

                password_check_msg_el.textContent = '암호가 일치하지 않습니다.';
                password_check_msg_el.style.color = 'red';
                password_check_msg_el.style.display = 'block';

            }




        })

        // 중복 체크 통과후 id 변경 방지
        document.getElementById('username').addEventListener('input', function() {

            is_id_check = false;

        })

        // ========================================================================

        document.getElementById('id-ch-btn').addEventListener('click', function() {

            let username = document.getElementById('username').value;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'idcheck.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {

                if (xhr.status === 200) {

                    let response = xhr.responseText;
                    console.log(response);
                    if (response == "true") {

                        console.log('중복 임')
                        is_id_check_ok('false');

                    } else if (response == "null") {

                        is_id_check_ok('null');

                    } else {

                        console.log('중복 아님')
                        is_id_check_ok('true');

                    }

                }

            };
            xhr.send('username=' + encodeURIComponent(username));

        })

        const body_main_el = document.getElementById('body main');
        const main_menu_list_el = document.querySelector('.main-menu-list');
        const c_menu_lists_el = document.querySelectorAll('.c-menu-list a');
        main_menu_list_el.addEventListener('mouseenter', () => {
            c_menu_lists_el.forEach((item, index) => {
                switch (index) {
                    // checklist
                    case 0:
                        // item.classList.remove('d-flex');
                        // item.classList.remove('justify-content-center');
                        item.innerHTML = '';
                        item.innerHTML = '<div class="d-flex"><span class="material-symbols-outlined">home</span> &nbsp;&nbsp;<span>메인 페이지</span></div>';
                        break;
                    case 1:
                        item.innerHTML = '';
                        item.innerHTML = '<div class="d-flex"><span class="material-symbols-outlined">edit_note</span> &nbsp;&nbsp;<span>문제 모음집</span></div>';
                        break;
                    case 2:
                        item.innerHTML = '';
                        item.innerHTML = '<div class="d-flex"><span class="material-symbols-outlined">sports_score</span> &nbsp;&nbsp;<span>스코어 보드</span></div>';
                        break;
                    case 3:
                        item.innerHTML = '';
                        item.innerHTML = '<div class="d-flex"><span class="material-symbols-outlined">finance</span> &nbsp;&nbsp;<span>사용자 통계</span></div>';
                        break;
                }


            });


            // body_main_el.classList.replace('col-10','col-9');
            main_menu_list_el.classList.replace('col-1', 'col-2'); // col-1을 col-2로 변경
        });

        main_menu_list_el.addEventListener('mouseleave', () => {
            c_menu_lists_el.forEach((item, index) => {
                switch (index) {

                    case 0:
                        // item.classList.add('d-flex');
                        // item.classList.add('justify-content-center');
                        item.innerHTML = '';
                        item.innerHTML = '<span class="material-symbols-outlined">home</span>';
                        break;
                    case 1:
                        item.innerHTML = '';
                        item.innerHTML = '<span class="material-symbols-outlined">edit_note</span>';
                        break;
                    case 2:
                        item.innerHTML = '';
                        item.innerHTML = '<span class="material-symbols-outlined">sports_score</span>';
                        break;
                    case 3:
                        item.innerHTML = '';
                        item.innerHTML = '<span class="material-symbols-outlined">finance</span>';
                        break;
                }


            });


            // body_main_el.classList.replace('col-9','col-10');
            main_menu_list_el.classList.replace('col-2', 'col-1'); // col-2를 col-1로 복원
        });

        // 문제 추가 컨트롤
        const sc_form_el = document.querySelector('#sc-form');
        const sc_form_labels_el = sc_form_el.querySelectorAll('label');
        const form_els = [];
        const form_labels = [];
        const sc_p_v_el = document.getElementById('sc-p-v');
        const sc_d_v_el = document.getElementById('sc-d-v');

        [...sc_form_el.elements].forEach(item => {

            form_els.push(item);

        })

        sc_form_labels_el.forEach(item => {
            form_labels.push(item);
        })

        // 0, 웹확인
        // 1, 제목
        // 2, ssh 링크
        // 3, web 링크
        // 4, 키 값
        // 5, 점수
        // 6, 난이도
        // 7, 설명
        // 8, 힌트
        // 9, 버튼

        const set_sc_form = () => {

            if (form_els[0].checked == true) {
                form_els[3].style.display = 'block';
                form_labels[3].style.display = 'block';

                form_els[2].style.display = 'none';
                form_labels[2].style.display = 'none';

                form_els[7].style.display = 'none';
                form_labels[7].style.display = 'none';

                form_els[8].style.display = 'none';
                form_labels[8].style.display = 'none';


            } else {
                form_els[3].style.display = 'none';
                form_labels[3].style.display = 'none';

                form_els[2].style.display = 'block';
                form_labels[2].style.display = 'block';

                form_els[7].style.display = 'block';
                form_labels[7].style.display = 'block';

                form_els[8].style.display = 'block';
                form_labels[8].style.display = 'block';

            }

        }

        const getpointview = () => {

            sc_p_v_el.textContent = form_els[5].value;

            if (form_els[6].value == 1) {

                sc_d_v_el.textContent = "Normal";
                sc_d_v_el.style.color = "black";

            } else if (form_els[6].value == 2) {

                sc_d_v_el.textContent = "Hard";
                sc_d_v_el.style.color = "red";

            } else {

                sc_d_v_el.textContent = "Easy";
                sc_d_v_el.style.color = "green";

            }

        }



        form_els[0].addEventListener('click', () => {

            set_sc_form();

        });

        form_els[5].addEventListener('input', () => {

            getpointview();

        })
        form_els[6].addEventListener('input', () => {

            getpointview();

        })




        // a_modal_el = new bootstrap.Modal(document.getElementById('alert-modal'));
        // document.getElementById('alert-modal-title').textContent = " . json_encode($modal_msg[1]) . ";
        // document.getElementById('alert-modal-body').textContent = " . json_encode($modal_msg[2]) . ";
        // document.getElementById('alert-modal-end').addEventListener('click',()=>{
        //     var mm = ". json_encode($modal_msg[3]) .";
        //     if(mm)
        //     location.reload();
        // });
        // a_modal_el.show();

        // a_modal_el = new bootstrap.Modal(document.getElementById('alert-modal'));
        // document.getElementById('alert-modal-title').textContent = " . json_encode($modal_msg[1]) . ";
        // document.getElementById('alert-modal-body').textContent = " . json_encode($modal_msg[2]) . ";
        // document.getElementById('alert-modal-end').addEventListener('click',()=>{var mm = ". json_encode($modal_msg[3]) .";if(mm)location.reload();});
        // a_modal_el.show();


        init();
    </script>



</body>

</html>

<?php

// alert 모달 호출기 call_alert_modal
// i = 0, 호출 여부 1, 제목 2, 내용
if ($call_alert_modal[0]) {

    echo "<script>a_modal_el = new bootstrap.Modal(document.getElementById('alert-modal'));
        document.getElementById('alert-modal-title').textContent = " . json_encode($call_alert_modal[1]) . ";
        document.getElementById('alert-modal-body').textContent = " . json_encode($call_alert_modal[2]) . ";
        document.getElementById('alert-modal-end').addEventListener('click',()=>{var mm = " . json_encode($call_alert_modal[3]) . ";if(mm)location.reload();});
        a_modal_el.show();</script>";
}

// if($_SESSION['signin_success'] = true){

//     echo "<script>signin_success = new bootstrap.Modal(document.getElementById('signinsuccessMidal'));signin_success.show();</script>";
//     $_SESSION['signin_success'] = false;

// }





?>