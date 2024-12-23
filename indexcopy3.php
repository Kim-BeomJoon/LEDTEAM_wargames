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
        /* 전체 배경 수정 */
        body {
            background-color: #0a0a0f;
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('/assets/images/476f6c42d7b37ac4396b3f92d34b1e6a.jpg');
            background-size: 100% auto;
            background-position: top center;
            background-repeat: no-repeat;
            background-color: #0a0a0f;
            color: #ffffff;
        }

        /* 메뉴바 메인 컨테이너 */
        .main-menu-list {
            background: rgba(13, 17, 23, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-right: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 5px 0 20px rgba(0, 195, 255, 0.1);
            min-height: 100vh;
        }

        /* MENU 타이틀과 ADMIN 타이틀 통일 */
        .bg-secondary.text-white,
        .admin_m.bg-secondary {
            background: rgba(0, 195, 255, 0.2) !important;
            border: 1px solid rgba(0, 195, 255, 0.3);
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
            margin-bottom: 15px;
        }

        /* 메인 콘텐츠 영역 */
        .col-10.bg-light {
            background: rgba(13, 17, 23, 0.7) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: #ffffff;
        }

        /* 메뉴 아이템 */
        .list-group-item {
            background: rgba(20, 25, 35, 0.6) !important;
            border: 1px solid rgba(0, 195, 255, 0.15) !important;
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
        }

        /* 메뉴 아이콘 */
        .material-symbols-outlined {
            color: #00c3ff;
            text-shadow: 0 0 8px rgba(0, 195, 255, 0.5);
            transition: all 0.3s ease;
        }

        /* 호버 효과 */
        .list-group-item:hover {
            background: rgba(0, 195, 255, 0.15) !important;
            border-color: rgba(0, 195, 255, 0.3) !important;
            transform: translateX(5px);
            box-shadow: 
                0 0 15px rgba(0, 195, 255, 0.2),
                inset 0 0 10px rgba(0, 195, 255, 0.1);
        }

        .list-group-item:hover .material-symbols-outlined {
            color: #ffffff;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.8);
        }

        /* 활성 메뉴 */
        .list-group-item.active {
            background: rgba(0, 195, 255, 0.2) !important;
            border-color: rgba(0, 195, 255, 0.4) !important;
            box-shadow: 
                0 0 20px rgba(0, 195, 255, 0.2),
                inset 0 0 15px rgba(0, 195, 255, 0.1);
        }

        /* TEAM LED 로고 스타일 */
        .neon-container {
            background: rgba(0, 0, 0, 0.6);
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid rgba(0, 195, 255, 0.3);
            box-shadow: 
                0 0 15px rgba(0, 195, 255, 0.2),
                inset 0 0 10px rgba(0, 195, 255, 0.1);
            display: flex;
            align-items: center;
            animation: borderGlow 2s infinite alternate;
        }

        .navbar-logo {
            width: 45px;
            height: 45px;
            margin-right: 15px;
            filter: drop-shadow(0 0 8px rgba(0, 195, 255, 0.5));
        }

        .neon-text {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: 2px;
            animation: textGlow 2s infinite alternate;
        }

        /* 폼 컨테이너 스타일 */
        .form-container {
            background: rgba(13, 17, 23, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 20px rgba(0, 195, 255, 0.1);
        }

        /* 라벨 스타일 */
        .form-label {
            color: #00c3ff;
            font-size: 0.9rem;
            letter-spacing: 1px;
            margin-bottom: 8px;
            text-shadow: 0 0 8px rgba(0, 195, 255, 0.5);
        }

        /* 입력 필드 스타일 */
        .form-control {
            background: rgba(20, 25, 35, 0.7) !important;
            border: 1px solid rgba(0, 195, 255, 0.2) !important;
            color: #ffffff !important;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(25, 30, 40, 0.8) !important;
            border-color: rgba(0, 195, 255, 0.4) !important;
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.2) !important;
            color: #ffffff !important;
        }

        /* 체크박스 스타일 */
        .form-check-input {
            background-color: rgba(20, 25, 35, 0.7);
            border-color: rgba(0, 195, 255, 0.3);
        }

        .form-check-input:checked {
            background-color: #00c3ff;
            border-color: #00c3ff;
            box-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.8);
        }

        /* 셀렉트 박스 스타일 */
        .form-select {
            background-color: rgba(20, 25, 35, 0.7) !important;
            border: 1px solid rgba(0, 195, 255, 0.2) !important;
            color: #ffffff !important;
        }

        .form-select:focus {
            border-color: rgba(0, 195, 255, 0.4) !important;
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.2) !important;
        }

        /* 버튼 스타일 */
        .btn-primary {
            background: rgba(0, 195, 255, 0.2);
            border: 1px solid rgba(0, 195, 255, 0.4);
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: rgba(0, 195, 255, 0.4);
            border-color: rgba(0, 195, 255, 0.6);
            box-shadow: 0 0 20px rgba(0, 195, 255, 0.3);
            transform: translateY(-2px);
        }

        /* 포인트와 난이도 표시 */
        #sc-p-v, #sc-d-v {
            color: #00c3ff;
            text-shadow: 0 0 8px rgba(0, 195, 255, 0.5);
            font-weight: 500;
        }

        /* 난이도별 색상 */
        #sc-d-v.easy {
            color: #00ff9d;
            text-shadow: 0 0 8px rgba(0, 255, 157, 0.5);
        }

        #sc-d-v.normal {
            color: #00c3ff;
            text-shadow: 0 0 8px rgba(0, 195, 255, 0.5);
        }

        #sc-d-v.hard {
            color: #ff3e3e;
            text-shadow: 0 0 8px rgba(255, 62, 62, 0.5);
        }

        /* 문제 추가 페이지 컨테너 스타일 수정 */
        #list-item .container {
            background: rgba(13, 17, 23, 0.8) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2) !important;
            border-radius: 10px;
            box-shadow: 
                0 0 20px rgba(0, 195, 255, 0.1),
                inset 0 0 20px rgba(0, 195, 255, 0.05);
        }

        /* 문제 추가 타이틀 */
        #list-item .fs-3 {
            color: #00c3ff;
            text-shadow: 
                0 0 5px rgba(0, 195, 255, 0.5),
                0 0 10px rgba(0, 195, 255, 0.3);
            letter-spacing: 2px;
        }

        /* 체크박스 컨테이너 */
        #list-item .form-check {
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(20, 25, 35, 0.4);
            border-radius: 8px;
            border: 1px solid rgba(0, 195, 255, 0.1);
        }

        /* WEB 체크박스 라벨 */
        #list-item .form-check-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        /* 체크박스 스위치 */
        #list-item .form-switch .form-check-input {
            background-color: rgba(0, 195, 255, 0.2);
            border-color: rgba(0, 195, 255, 0.4);
        }

        #list-item .form-switch .form-check-input:checked {
            background-color: #00c3ff;
            border-color: #00c3ff;
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.5);
        }

        /* 입력 필드 그룹 간격 */
        #list-item .form-control,
        #list-item .form-range {
            margin-bottom: 20px;
        }

        /* 레인지 슬라이더 스타일 */
        #list-item .form-range::-webkit-slider-thumb {
            background: #00c3ff;
            box-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        /* 버튼 컨테이너 */
        #list-item .d-flex.justify-content-center {
            margin-top: 20px;
        }

        /* 로그인 모달 스타일링 */
        .modal-content {
            background: rgba(13, 17, 23, 0.95) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 195, 255, 0.2);
            box-shadow: 
                0 0 30px rgba(0, 195, 255, 0.2),
                inset 0 0 20px rgba(0, 195, 255, 0.1);
        }

        /* 모달 헤더 */
        .modal-header {
            border-bottom: 1px solid rgba(0, 195, 255, 0.2);
        }

        .modal-title {
            color: #00c3ff;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
            letter-spacing: 2px;
        }

        /* 모달 입력 필드 */
        .modal-body .form-control {
            background: rgba(20, 25, 35, 0.7);
            border: 1px solid rgba(0, 195, 255, 0.2);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .modal-body .form-control:focus {
            background: rgba(25, 30, 40, 0.8);
            border-color: rgba(0, 195, 255, 0.4);
            box-shadow: 0 0 15px rgba(0, 195, 255, 0.2);
            color: #ffffff;
        }

        /* 모달 라벨 */
        .modal-body label {
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 0 5px rgba(0, 195, 255, 0.3);
        }

        /* 모달 푸터 */
        .modal-footer {
            border-top: 1px solid rgba(0, 195, 255, 0.2);
        }

        /* 모달 버튼 */
        .modal-footer .btn-primary {
            background: rgba(0, 195, 255, 0.2);
            border: 1px solid rgba(0, 195, 255, 0.4);
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .modal-footer .btn-primary:hover {
            background: rgba(0, 195, 255, 0.4);
            border-color: rgba(0, 195, 255, 0.6);
            box-shadow: 0 0 20px rgba(0, 195, 255, 0.3);
            transform: translateY(-2px);
        }

        /* 닫기 버튼 */
        .btn-close {
            filter: invert(1) drop-shadow(0 0 5px rgba(0, 195, 255, 0.5));
        }

        /* 네온 테두리 애니메이션 */
        @keyframes borderGlow {
            from {
                box-shadow: 
                    0 0 15px rgba(0, 195, 255, 0.2),
                    inset 0 0 10px rgba(0, 195, 255, 0.1);
                border-color: rgba(0, 195, 255, 0.3);
            }
            to {
                box-shadow: 
                    0 0 25px rgba(0, 195, 255, 0.4),
                    inset 0 0 20px rgba(0, 195, 255, 0.2);
                border-color: rgba(0, 195, 255, 0.6);
            }
        }

        /* 텍스트 네온 효과 애니메이션 */
        @keyframes textGlow {
            from {
                text-shadow: 
                    0 0 10px rgba(0, 195, 255, 0.5),
                    0 0 20px rgba(0, 195, 255, 0.3),
                    0 0 30px rgba(0, 195, 255, 0.2);
            }
            to {
                text-shadow: 
                    0 0 20px rgba(0, 195, 255, 0.8),
                    0 0 30px rgba(0, 195, 255, 0.6),
                    0 0 40px rgba(0, 195, 255, 0.4);
            }
        }

        /* 본문 컨테이너 네온 효과 */
        .main {
            border: 1px solid rgba(0, 195, 255, 0.3);
            box-shadow: 
                0 0 20px rgba(0, 195, 255, 0.3),
                inset 0 0 30px rgba(0, 195, 255, 0.1);
            animation: mainBorderGlow 3s infinite alternate;
        }

        @keyframes mainBorderGlow {
            from {
                box-shadow: 
                    0 0 20px rgba(0, 195, 255, 0.3),
                    inset 0 0 30px rgba(0, 195, 255, 0.1);
                border-color: rgba(0, 195, 255, 0.3);
            }
            to {
                box-shadow: 
                    0 0 35px rgba(0, 195, 255, 0.5),
                    inset 0 0 50px rgba(0, 195, 255, 0.2);
                border-color: rgba(0, 195, 255, 0.6);
            }
        }

        /* 로고 및 텍스트 네온 효과 강화 */
        .neon-container {
            background: rgba(0, 0, 0, 0.6);
            padding: 12px 20px;
            border-radius: 10px;
            border: 2px solid rgba(0, 195, 255, 0.5);
            box-shadow: 
                0 0 20px rgba(0, 195, 255, 0.4),
                inset 0 0 15px rgba(0, 195, 255, 0.2);
            display: flex;
            align-items: center;
            animation: logoGlow 1.5s infinite alternate;
        }

        /* 로고 이미지 */
        .navbar-logo {
            width: 45px;
            height: 45px;
            margin-right: 15px;
            filter: drop-shadow(0 0 10px rgba(0, 195, 255, 0.8));
            animation: logoImageGlow 1.5s infinite alternate;
        }

        /* LED TEAM 텍스트 */
        .neon-text {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: 2px;
            animation: textStrongGlow 1.5s infinite alternate;
        }

        /* 로고 컨테이너 강화된 네온 효과 */
        @keyframes logoGlow {
            from {
                box-shadow: 
                    0 0 20px rgba(0, 195, 255, 0.4),
                    inset 0 0 15px rgba(0, 195, 255, 0.2);
                border-color: rgba(0, 195, 255, 0.5);
            }
            to {
                box-shadow: 
                    0 0 40px rgba(0, 195, 255, 0.7),
                    inset 0 0 30px rgba(0, 195, 255, 0.4);
                border-color: rgba(0, 195, 255, 1);
            }
        }

        /* 로고 이미지 강화된 네온 효과 */
        @keyframes logoImageGlow {
            from {
                filter: drop-shadow(0 0 10px rgba(0, 195, 255, 0.8));
            }
            to {
                filter: drop-shadow(0 0 20px rgba(0, 195, 255, 1));
            }
        }

        /* LED TEAM 텍스트 강화된 네온 효과 */
        @keyframes textStrongGlow {
            from {
                text-shadow: 
                    0 0 10px rgba(0, 195, 255, 0.8),
                    0 0 20px rgba(0, 195, 255, 0.5),
                    0 0 30px rgba(0, 195, 255, 0.3);
            }
            to {
                text-shadow: 
                    0 0 20px #00c3ff,
                    0 0 40px #00c3ff,
                    0 0 60px rgba(0, 195, 255, 0.8),
                    0 0 80px rgba(0, 195, 255, 0.5);
            }
        }

        /* 네비게이션 바 컨테이너에 레이저 효과 추가 */
        .navbar {
            position: relative;
            overflow: hidden;
        }

        /* 레이저 빔 효과 */
        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(
                90deg,
                transparent,
                transparent 50%,
                #00c3ff 50%,
                #00c3ff 65%,
                transparent 65%
            );
            animation: laserBeam 4s infinite;
        }

        .navbar::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(
                90deg,
                transparent,
                transparent 50%,
                #00c3ff 50%,
                #00c3ff 65%,
                transparent 65%
            );
            animation: laserBeamReverse 4s infinite;
        }

        /* 레이저 빔 애니메이션 */
        @keyframes laserBeam {
            0% {
                left: -100%;
                opacity: 0;
            }
            20% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                left: 100%;
                opacity: 0;
            }
        }

        @keyframes laserBeamReverse {
            0% {
                right: -100%;
                opacity: 0;
            }
            20% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                right: 100%;
                opacity: 0;
            }
        }

        /* 로고 컨테이너에도 레이저 효과 추가 */
        .neon-container {
            position: relative;
            overflow: hidden;
        }

        .neon-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(
                90deg,
                transparent,
                #00c3ff 20%,
                #00c3ff 40%,
                transparent 60%
            );
            animation: logoLaserBeam 3s infinite;
            filter: blur(1px);
        }

        @keyframes logoLaserBeam {
            0% {
                left: -100%;
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                left: 100%;
                opacity: 0;
            }
        }

        /* 추가적인 광선 효과 */
        .laser-glow {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            background: radial-gradient(
                circle at var(--x, 50%) var(--y, 50%),
                rgba(0, 195, 255, 0.15) 0%,
                transparent 60%
            );
            opacity: 0;
            transition: opacity 0.3s;
        }

        /* 메인 컨텐츠 스타일링 */
        .main-content {
            padding: 2rem;
            background: rgba(13, 17, 23, 0.8);
            backdrop-filter: blur(10px);
        }

        /* 히어로 섹션 */
        .hero-section {
            text-align: center;
            padding: 4rem 0;
            position: relative;
        }

        .cyber-glitch-title h1 {
            font-size: 4rem;
            color: #00c3ff;
            text-shadow: 
                0 0 10px rgba(0, 195, 255, 0.8),
                0 0 20px rgba(0, 195, 255, 0.5),
                0 0 30px rgba(0, 195, 255, 0.3);
            animation: titleGlow 2s infinite alternate;
        }

        .cyber-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 1rem;
        }

        /* 통계 박스 */
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 3rem;
        }

        .stat-box {
            background: rgba(0, 195, 255, 0.1);
            border: 1px solid rgba(0, 195, 255, 0.3);
            padding: 1.5rem;
            border-radius: 10px;
            min-width: 200px;
            animation: statGlow 3s infinite alternate;
        }

        .stat-number {
            display: block;
            font-size: 2.5rem;
            color: #00c3ff;
            font-weight: bold;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        /* 최근 활동 섹션 */
        .recent-activities {
            margin-top: 4rem;
        }

        .section-title {
            color: #00c3ff;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .activity-card {
            background: rgba(20, 25, 35, 0.7);
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .activity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 20px rgba(0, 195, 255, 0.3);
        }

        /* 애니메이션 */
        @keyframes titleGlow {
            from {
                text-shadow: 
                    0 0 10px rgba(0, 195, 255, 0.8),
                    0 0 20px rgba(0, 195, 255, 0.5);
            }
            to {
                text-shadow: 
                    0 0 20px rgba(0, 195, 255, 1),
                    0 0 40px rgba(0, 195, 255, 0.8),
                    0 0 60px rgba(0, 195, 255, 0.5);
            }
        }

        @keyframes statGlow {
            from {
                box-shadow: 
                    0 0 10px rgba(0, 195, 255, 0.2),
                    inset 0 0 5px rgba(0, 195, 255, 0.1);
            }
            to {
                box-shadow: 
                    0 0 20px rgba(0, 195, 255, 0.4),
                    inset 0 0 15px rgba(0, 195, 255, 0.2);
            }
        }

        /* 공지사항 섹션 */
        .notice-section {
            margin: 3rem 0;
            padding: 2rem;
            background: rgba(13, 17, 23, 0.7);
            border-radius: 15px;
            border: 1px solid rgba(0, 195, 255, 0.2);
        }

        .notice-grid {
            display: grid;
            gap: 1.5rem;
        }

        .notice-card {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            background: rgba(20, 25, 35, 0.6);
            border-radius: 10px;
            border: 1px solid rgba(0, 195, 255, 0.15);
            position: relative;
            transition: all 0.3s ease;
        }

        .notice-card:hover {
            transform: translateX(10px);
            border-color: rgba(0, 195, 255, 0.4);
            box-shadow: 0 0 20px rgba(0, 195, 255, 0.2);
        }

        /* 도전 과제 섹션 */
        .challenge-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .challenge-card {
            background: rgba(20, 25, 35, 0.7);
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 1.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .challenge-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 25px rgba(0, 195, 255, 0.3);
        }

        /* 난이도 배지 */
        .difficulty {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .difficulty.easy {
            background: rgba(0, 255, 0, 0.2);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00ff00;
        }

        .difficulty.medium {
            background: rgba(255, 165, 0, 0.2);
            border: 1px solid rgba(255, 165, 0, 0.3);
            color: #ffa500;
        }

        .difficulty.hard {
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid rgba(255, 0, 0, 0.3);
            color: #ff0000;
        }

        /* 랭커 섹션 */
        .ranker-card {
            background: rgba(20, 25, 35, 0.8);
            border: 1px solid rgba(0, 195, 255, 0.2);
            padding: 1.5rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 2rem;
            transition: all 0.3s ease;
        }

        .ranker-card.first {
            background: linear-gradient(45deg, rgba(20, 25, 35, 0.9), rgba(0, 195, 255, 0.1));
            border-color: rgba(0, 195, 255, 0.4);
        }

        /* 아이콘 스타일 */
        .material-symbols-outlined {
            vertical-align: middle;
            margin-right: 0.5rem;
            color: #00c3ff;
        }

        /* 섹션 타이틀 공통 스타일 */
        .section-title {
            font-size: 1.8rem;
            color: #00c3ff;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(0, 195, 255, 0.2);
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        /* 챌린지 타입 배지 */
        .challenge-type {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .challenge-type.web {
            background: rgba(0, 150, 255, 0.2);
            border: 1px solid rgba(0, 150, 255, 0.3);
            color: #0096ff;
        }

        .challenge-type.pwn {
            background: rgba(255, 0, 150, 0.2);
            border: 1px solid rgba(255, 0, 150, 0.3);
            color: #ff0096;
        }

        .challenge-type.crypto {
            background: rgba(150, 255, 0, 0.2);
            border: 1px solid rgba(150, 255, 0, 0.3);
            color: #96ff00;
        }

        /* 포인트 표시 */
        .points {
            color: #00c3ff;
            font-weight: 500;
        }

        /* 활동 카드 아이콘 */
        .activity-card .material-symbols-outlined {
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        /* 문제 리스트 컨테이너 */
        .problem-list-container {
            padding: 20px;
        }

        /* 문제 카드 스타일링 */
        .problem-card {
            background: rgba(13, 17, 23, 0.7);
            border: 1px solid rgba(0, 195, 255, 0.2);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .problem-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 195, 255, 0.6);
            box-shadow: 
                0 0 20px rgba(0, 195, 255, 0.2),
                inset 0 0 10px rgba(0, 195, 255, 0.1);
        }

        /* 문제 제목 */
        .problem-card .card-title {
            color: #00c3ff;
            font-size: 1.4rem;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(0, 195, 255, 0.5);
        }

        /* 문제 정보 배지 */
        .problem-badge {
            padding: 0.4rem 1rem;
            border-radius: 15px;
            font-size: 0.85rem;
            margin-right: 10px;
            display: inline-block;
        }

        .problem-badge.type {
            background: rgba(0, 195, 255, 0.15);
            border: 1px solid rgba(0, 195, 255, 0.3);
            color: #00c3ff;
        }

        .problem-badge.difficulty-easy {
            background: rgba(0, 255, 0, 0.15);
            border: 1px solid rgba(0, 255, 0, 0.3);
            color: #00ff00;
        }

        .problem-badge.difficulty-normal {
            background: rgba(255, 165, 0, 0.15);
            border: 1px solid rgba(255, 165, 0, 0.3);
            color: #ffa500;
        }

        .problem-badge.difficulty-hard {
            background: rgba(255, 0, 0, 0.15);
            border: 1px solid rgba(255, 0, 0, 0.3);
            color: #ff0000;
        }

        /* 점수 표시 */
        .problem-points {
            font-size: 1.2rem;
            color: #00c3ff;
            text-shadow: 0 0 5px rgba(0, 195, 255, 0.5);
        }

        /* 문제 링크 */
        .problem-link {
            text-decoration: none;
            color: inherit;
        }

        .problem-link:hover {
            color: inherit;
        }

        /* 레이저 라인 효과 */
        .problem-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(
                90deg,
                transparent,
                #00c3ff 20%,
                #00c3ff 40%,
                transparent 60%
            );
            animation: laserLine 3s infinite;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .problem-card:hover::before {
            opacity: 1;
        }

        @keyframes laserLine {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }
        

        /* 해결된 문제 표시 */
        .problem-card.solved {
            border-color: rgba(0, 255, 0, 0.3);
        }

        .problem-card.solved::after {
            content: '✓';
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: #00ff00;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
            font-size: 1.2rem;
        }
    </style>
</head>





<body class="" style="background-color: #3a3a3a ;">



    <nav class="navbar navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand neon-sign" href="#">
                <div class="neon-container">
                    <img src="/assets/images/LEDTEAM_로고.jpg" alt="TEAM LED Logo" class="navbar-logo">
                    <span class="neon-text">TEAM LED</span>
                </div>
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

            <!-- style="background-color: #3d3d3d ; color: #e6e6e6;" -->

            <div class="col-10 bg-light" id="body main">

                <div>

                    <div class="row">
                        <div class="col pt-3" style="min-height: 100vh;">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                                    <!-- 히어로 섹션 -->
                                    <div class="hero-section">
                                        <div class="cyber-glitch-title text-center mb-4">
                                            <h1 class="display-4 mb-3">TEAM LED SECURITY</h1>
                                            <p class="cyber-subtitle">Cyber Security Research & Development</p>
                                        </div>
                                        
                                        <!-- 통계 카드 -->
                                        <div class="stats-container">
                                            <div class="stat-box">
                                                <span class="stat-number" id="totalChallenges">24</span>
                                                <span class="stat-label">Total Challenges</span>
                                            </div>
                                            <div class="stat-box">
                                                <span class="stat-number" id="activeUsers">127</span>
                                                <span class="stat-label">Active Users</span>
                                            </div>
                                            <div class="stat-box">
                                                <span class="stat-number" id="totalSolves">1,337</span>
                                                <span class="stat-label">Total Solves</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 최근 도전 과제 -->
                                    <div class="challenges-section mt-5">
                                        <h2 class="section-title">
                                            <span class="material-symbols-outlined">terminal</span>
                                            Latest Challenges
                                        </h2>
                                        <div class="challenge-grid">
                                            <div class="challenge-card">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="challenge-type web">WEB</span>
                                                    <span class="difficulty easy">Easy</span>
                                                </div>
                                                <h3>SQL Injection Basic</h3>
                                                <p class="text-muted small mb-3">Test your SQL injection skills...</p>
                                                <div class="d-flex justify-content-between">
                                                    <span class="points">10 pts</span>
                                                    <span class="solve-count">Solves: 23</span>
                                                </div>
                                            </div>
                                            <div class="challenge-card">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="challenge-type pwn">PWN</span>
                                                    <span class="difficulty medium">Medium</span>
                                                </div>
                                                <h3>Buffer Overflow 101</h3>
                                                <p class="text-muted small mb-3">Classic buffer overflow challenge...</p>
                                                <div class="d-flex justify-content-between">
                                                    <span class="points">20 pts</span>
                                                    <span class="solve-count">Solves: 15</span>
                                                </div>
                                            </div>
                                            <div class="challenge-card">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="challenge-type crypto">CRYPTO</span>
                                                    <span class="difficulty hard">Hard</span>
                                                </div>
                                                <h3>Advanced RSA</h3>
                                                <p class="text-muted small mb-3">Can you break this encryption?</p>
                                                <div class="d-flex justify-content-between">
                                                    <span class="points">30 pts</span>
                                                    <span class="solve-count">Solves: 8</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 최근 활동 -->
                                    <div class="recent-activities mt-5">
                                        <h2 class="section-title">
                                            <span class="material-symbols-outlined">history</span>
                                            Recent Activities
                                        </h2>
                                        <div class="activity-grid">
                                            <div class="activity-card">
                                                <span class="material-symbols-outlined text-success">task_alt</span>
                                                <div>
                                                    <strong>CyberMaster</strong> solved <span class="text-info">SQL Injection Basic</span>
                                                    <div class="text-muted small">2 minutes ago</div>
                                                </div>
                                            </div>
                                            <div class="activity-card">
                                                <span class="material-symbols-outlined text-success">task_alt</span>
                                                <div>
                                                    <strong>H4cker101</strong> solved <span class="text-info">Buffer Overflow 101</span>
                                                    <div class="text-muted small">5 minutes ago</div>
                                                </div>
                                            </div>
                                            <div class="activity-card">
                                                <span class="material-symbols-outlined text-success">task_alt</span>
                                                <div>
                                                    <strong>SecurityPro</strong> solved <span class="text-info">Advanced RSA</span>
                                                    <div class="text-muted small">10 minutes ago</div>
                                                </div>
                                            </div>
                                        </div>
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
                        s_box.className = "col-2 mb-2 mx-1 pb-3 plist border border-3 border-secondary";
                        // 최소/최대 너비 설정으로 박스 크기 제어
                        s_box.style.minWidth = "220px";
                        s_box.style.maxWidth = "245px";




                        var s_title_box = document.createElement('div');
                        s_title_box.className = "d-flex justify-content-center bg-danger";
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
                id_check_msg_el.textContent = '사용 가능한 Id 입다.';
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
                    id_check_msg_el.textContent = '중복체크 하야합니다!.';

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

        // 제 추가 컨트롤
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
        // 5, 수
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
            
            sc_d_v_el.classList.remove('easy', 'normal', 'hard');
            
            if (form_els[6].value == 1) {
                sc_d_v_el.textContent = "Normal";
                sc_d_v_el.classList.add('normal');
            } else if (form_els[6].value == 2) {
                sc_d_v_el.textContent = "Hard";
                sc_d_v_el.classList.add('hard');
            } else {
                sc_d_v_el.textContent = "Easy";
                sc_d_v_el.classList.add('easy');
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

        // 마우스 움직임에 따른 광선 효과
        document.querySelector('.navbar').addEventListener('mousemove', (e) => {
            const rect = e.target.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            
            const glow = e.target.querySelector('.laser-glow');
            if (glow) {
                glow.style.setProperty('--x', `${x}%`);
                glow.style.setProperty('--y', `${y}%`);
                glow.style.opacity = '1';
            }
        });

        document.querySelector('.navbar').addEventListener('mouseleave', (e) => {
            const glow = e.target.querySelector('.laser-glow');
            if (glow) {
                glow.style.opacity = '0';
            }
        });
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
        document.getElementById('alert-modal-end').addEventListener('click',()=>{var mm = " . json_encode($call_alert_modal[3]) .";if(mm)location.reload();});
        a_modal_el.show();</script>";
}

// if($_SESSION['signin_success'] = true){

//     echo "<script>signin_success = new bootstrap.Modal(document.getElementById('signinsuccessMidal'));signin_success.show();</script>";
//     $_SESSION['signin_success'] = false;

// }






?>