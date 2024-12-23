<?php


session_start();

include './db/maindb.php';

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


                $stmt = $conn->prepare("INSERT INTO challenges_data VALUES(null,?,null,?,?,null,?,?,?,null,0)");
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
    <title>LED WARGAME!</title>
    <!-- bootstrap 5 -->
    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- google font -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- Chart.js cdn -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <link rel="stylesheet" href="./css/index.css">
</head>





<body class="" style="background-color: #3a3a3a ;">


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
                    <button class="btn btn-success" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">회원가입</button>
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
                    <button class="btn btn-danger" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">돌아가기</button>
                </div>
            </div>
        </div>
    </div>

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


    <nav class="navbar navbar-ligh bg-dark text-whit nav-top" style="">
        <div class="container-fluid">
            <a class="navbar-brand text-white neon" href="#">
                <img src="/assets/images/LEDTEAM_로고.jpg" alt="TEAM LED Logo" class="navbar-logo"
                    class="d-inline-block align-text-top">
                TEAM LED
            </a>

            <div>
                <div class="d-flex">
                    <span class="me-4 pt-1" id="n-name"></span>
                    <span class="me-4 pt-1" id="n-point"></span>
                    <button type="button" class="btn btn-success me-4" id="n-ch-btn">채팅 <span class="badge bg-secondary">0</span></button>
                    <button type="button" class="btn btn-primary me-5" id="n-logout-btn">로그아웃</button>
                </div>





                <a class="btn btn-primary me-5" data-bs-toggle="modal" href="#exampleModalToggle" role="button" id="n-login">로그인</a>






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
                        <a class="list-group-item list-group-item-action list-group-item-secondary d-flex justify-content-center" id="list-challenge-list"
                            data-bs-toggle="list" href="#list-challenge" role="tab" aria-controls="list-challenge"><span class="material-symbols-outlined">edit_note</span></a>
                        <a class="list-group-item list-group-item-action list-group-item-secondary d-flex justify-content-center" id="list-score-list"
                            data-bs-toggle="list" href="#list-score" role="tab"
                            aria-controls="list-score"><span class="material-symbols-outlined">sports_score</span></a>
                        <a class="list-group-item list-group-item-action list-group-item-secondary d-flex justify-content-center" id="list-userdata-list"
                            data-bs-toggle="list" href="#list-userdata" role="tab"
                            aria-controls="list-userdata"><span class="material-symbols-outlined">finance</span></a>
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
                                <div class="tab-pane fade show active" id="list-home" role="tabpanel"
                                    aria-labelledby="list-home-list">

                                    <!-- 히어로 섹션 -->
                                    <div class="hero-section">
                                        <div class="cyber-glitch-title text-center mb-4">
                                            <h1 class="display-4 mb-3">TEAM LED SECURITY</h1>
                                            <p class="cyber-subtitle">Cyber Security Research & Development</p>
                                        </div>

                                        <!-- 통계 카드 -->
                                        <div id="main-stats-container" class="stats-container">
                                            <div class="stat-box">
                                                <span class="stat-number" id="totalChallenges">0</span>
                                                <span class="stat-label">Total Challenges</span>
                                            </div>
                                            <div class="stat-box">
                                                <span class="stat-number" id="activeUsers">0</span>
                                                <span class="stat-label">Signing Users</span>
                                            </div>
                                            <div class="stat-box">
                                                <span class="stat-number" id="totalSolves">0</span>
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

                                        </div>
                                    </div>

                                    <!-- 최근 활동 -->
                                    <div class="recent-activities mt-5">
                                        <h2 class="section-title">
                                            <span class="material-symbols-outlined">history</span>
                                            Recent Activities
                                        </h2>
                                        <div class="activity-grid">

                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="list-challenge" role="tabpanel" aria-labelledby="list-challenge-list">
                                    <div class="container-fluid px-4"> <!-- container를 container-fluid로 변경하고 패딩 추가 -->
                                        <div class="row justify-content-start g-2" id="c-list">
                                            <!-- 문제 박스들이 여기에 추가됨 -->
                                        </div>
                                    </div>


                                </div>
                                <div class="tab-pane fade" id="list-score" role="tabpanel" aria-labelledby="list-score-list">
                                    <!-- 스코어보드 컨테이너 -->
                                    <div class="scoreboard-container">
                                        <h2 class="scoreboard-title">
                                            <span class="material-symbols-outlined">military_tech</span>
                                            Top Hackers
                                        </h2>

                                        <!-- 스코어보드 테이블 -->
                                        <div class="score-table-container">
                                            <table class="score-table">
                                                <thead>
                                                    <tr>
                                                        <th>Rank</th>
                                                        <th>Hacker</th>
                                                        <th>Points</th>
                                                        <th>Challenges</th>
                                                        <th>Last Active</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="score-table-body">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="list-userdata" role="tabpanel" aria-labelledby="list-userdata-list">
                                    <!-- 유저 통계  -->
                                    <div id="lu-fullPage">
                                        <div id="lu-fullPage-1" class="section">
                                        </div>
                                        <div id="lu-fullPage-2" class="section">
                                        </div>
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





                    </div>

                </div>

                <div class="col-1"></div>



            </div>

            <!-- 채팅창 컨테이너 -->
            <div id="chat-box" class="chat-container">
                <div class="chat-header">
                    <span>채팅</span>
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="chat-messages" id="chat-messages">
                    <!-- 채팅 메시지들이 여기에 표시됨 -->
                </div>
                <div class="chat-input-container">
                    <input type="text" class="chat-input" id="chat-input" placeholder="메시지를 입력하세요...">
                    <button class="chat-send-btn">
                        <span class="material-symbols-outlined" id="chat-send-btn">send</span>
                    </button>
                </div>
            </div>
        </div>

    </div>











    <script>
        const n_name_el = document.getElementById('n-name');
        const n_point_el = document.getElementById('n-point');
        const n_login_el = document.getElementById('n-login');
        const n_ch_btn_el = document.getElementById('n-ch-btn');
        const n_logout_btn_el = document.getElementById('n-logout-btn');
        const challenge_grid_el = document.querySelector('.challenge-grid');
        const main_stats_el = document.querySelectorAll('#main-stats-container .stat-box .stat-number');
        const activity_grid_el = document.querySelector('.activity-grid');
        const score_table_body_el = document.querySelector('#score-table-body');
        let is_id_check = false;
        let is_pass_check = false;

        // 경고 모달 호출
        const call_alert_modal = (title, body, end) => {


            a_modal_el = new bootstrap.Modal(document.getElementById('alert-modal'));
            document.getElementById('alert-modal-title').textContent = title;
            document.getElementById('alert-modal-body').textContent = body;
            document.getElementById('alert-modal-end').addEventListener('click', () => {
                var mm = end;
                if (mm) location.reload();
            });
            a_modal_el.show();
        }

        const set_main_data = () => {

            let u_point = 0;

            fetch('/php/getUChallengesData.php')
                .then(response => response.json())
                .then(data => {
                    // console.log(data);

                    // 초기화
                    challenge_grid_el.innerHTML = '';

                    // 최근것 3개 까지만 출력
                    let limit = 3;
                    if (data.length > 0) {


                        for (let i = 0; i < data.length; i++) {

                            if (i < limit) {
                                const card = createChallengeCard(data[i]);


                                challenge_grid_el.appendChild(card);
                                u_point += data[i].c_point;

                            } else {
                                u_point += data[i].c_point;

                            }

                            // 포인트 총합 출력
                            n_name_el.textContent = "<?php echo $_SESSION['nickname']; ?>"
                            // n_point_el.textContent = "포인트 : " + "<?php echo $_SESSION['u_point']; ?>"
                            n_point_el.textContent = "포인트 : " + u_point;



                        }


                    } else {
                        // 만약 새로 생성한 계정 이기때문에 getUChallengesData 이 값을 불러오지 못한다면.
                        // 포인트 총합 출력
                        n_name_el.textContent = "<?php echo $_SESSION['nickname']; ?>"
                        // n_point_el.textContent = "포인트 : " + "<?php echo $_SESSION['u_point']; ?>"
                        n_point_el.textContent = "포인트 : " + u_point;


                    }



                })


        }

        const set_main_stats = () => {

            // 메인 통계 정보 출력
            fetch('/php/getMainData.php') // PHP 파일 경로로 변경
                .then(response => response.json())
                .then(data => {
                    // 메인 통계 정보 출력

                    main_stats_el.forEach((item, i) => {
                        if (i == 0) {
                            item.textContent = data.Ccount;
                        } else if (i == 1) {
                            item.textContent = data.Ucount;
                        } else if (i == 2) {
                            item.textContent = data.Tsolves;
                        }
                    });

                })
                .catch(error => console.error('Error:', error));

        }

        const c_list_el = document.getElementById('c-list');
        setChallenges = () => {

            fetch('/php/getChallenges.php') // PHP 파일 경로로 변경
                .then(response => response.json())
                .then(data => {
                    // console.log(data); 

                    data.forEach(item => {

                        var c_box = document.createElement('div');
                        c_box.className = "col-2 mb-2 mx-1 pb-2 pt-1 plist c_box";

                        var c_title_box = document.createElement('div');
                        c_title_box.className = "d-flex justify-content-center p-2 c_title_box";

                        c_title_box.addEventListener('click', () => {

                            if ("<?php echo $_SESSION['user_access']; ?>" == true) {
                                window.open(item.c_link, '_blank');
                            } else {
                                call_alert_modal('경고', '로그인 되지 않았습니다.', false);
                            }


                        })

                        var c_title = document.createElement('span');
                        c_title.textContent = item.c_title;
                        c_title.className = "fw-bold fs-5 text-white";

                        var c_content_box = document.createElement('div');
                        c_content_box.className = "p-3";

                        var c_label = document.createElement('label');
                        c_label.className = "form-label d-flex justify-content-between mb-2";
                        c_label.setAttribute("for", "sol" + item.c_id);
                        // c_label.innerHTML = `
                        //     <span class="badge bg-secondary">난이도: ${item.c_difficulty}</span>
                        //     <span class="badge bg-info">점수: ${item.c_point}</span>
                        // `;
                        c_label.innerHTML = `
                            <span class="challenge-badge badge difficulty-${item.c_difficulty.toLowerCase()}">${item.c_difficulty}</span>
                            <span class="challenge-badge badge points">${item.c_point} pts</span>
                            
                        `;

                        var c_input = document.createElement('input');
                        c_input.className = "form-control ptext mt-4";
                        c_input.id = "sol" + item.c_id;
                        c_input.style.transition = "all 0.3s ease";

                        if (item.c_clear == 'true') {

                            c_title_box.style.background = "rgba(0, 255, 0, 0.6)";
                            c_title_box.style.border = "1px solid rgba(0, 255, 0, 0.8)";
                            c_box.style.border = "1px solid rgba(0, 255, 0, 0.5)";
                            c_box.style.boxShadow = "0 0 10px rgba(0, 255, 0, 0.5)";
                            c_box.style.animation = "statGlowgreen 3s infinite alternate";
                            c_input.value = 'CLEAR!';
                            c_input.type = 'text';
                            c_input.readOnly = true;



                        } else {


                            c_input.setAttribute('placeholder', "key input");
                            c_input.setAttribute('type', "password");
                            c_input.addEventListener('click', () => {
                                if (!"<?php echo $_SESSION['user_access']; ?>" == true) {
                                    call_alert_modal('경고', '키값을 입력하려면 로그인 해야합니다.', false);
                                }
                            });
                            c_input.addEventListener('input', function() {

                                // 암호 코드는 글자수가 30글자 이므로 30 글자 일때만 동작
                                if (c_input.value.length == 30) {


                                    if ("<?php echo $_SESSION['user_access']; ?>" == true) {

                                        let key = c_input.value;
                                        var xhr = new XMLHttpRequest();
                                        xhr.open('POST', '/php/setkey.php', true);
                                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                                        xhr.onload = function() {

                                            if (xhr.status === 200) {

                                                let response = xhr.responseText;
                                                if (response === 'ok') {

                                                    // console.log('통과')
                                                    c_title_box.style.background = "rgba(0, 255, 0, 0.6)";
                                                    c_title_box.style.border = "1px solid rgba(0, 255, 0, 0.8)";
                                                    c_box.style.border = "1px solid rgba(0, 255, 0, 0.5)";
                                                    c_box.style.boxShadow = "0 0 10px rgba(0, 255, 0, 0.5)";
                                                    c_box.style.animation = "statGlowgreen 3s infinite alternate";
                                                    c_input.value = 'CLEAR!';
                                                    c_input.type = 'text';
                                                    c_input.readOnly = true;
                                                    // 메인 데이터 출력
                                                    set_main_data();
                                                    // 메인 통계 출력
                                                    set_main_stats();


                                                }


                                            }

                                        };
                                        const jsonData = JSON.stringify({

                                            username: "<?php echo $_SESSION['username']; ?>",
                                            c_id: item.c_id,
                                            key: key

                                        });

                                        xhr.send(jsonData);



                                    }






                                }


                            });



                        }



                        c_box.appendChild(c_title_box);
                        c_box.appendChild(c_content_box);
                        c_title_box.appendChild(c_title);
                        c_content_box.appendChild(c_label);
                        c_content_box.appendChild(c_input);
                        c_list_el.appendChild(c_box);




                        // const plist_el = document.querySelector('.plist');
                        // const ptext_el = document.querySelector('.ptext');
                        // ptext_el.addEventListener('input', function() {



                        // });

                    });

                })
                .catch(error => console.error('Error:', error));







        }

        const list_score_list_el = document.getElementById('list-score-list');

        // 스코어 카드 생성기
        function createScoreCard(data, rank) {

            // 랭크 번호 컨테이너
            const card = document.createElement('tr');
            card.className = 'rank-' + rank;

            // 랭크 번호 표시
            const rank_td = document.createElement('td');
            rank_td.textContent = rank;

            // 유저 이름 표시
            const user_td = document.createElement('td');
            user_td.textContent = data.nickname;


            // 포인트 표시
            const point_td = document.createElement('td');
            point_td.textContent = data.total_point + "/" + data.Tpoint;

            // 해결 문제 표시
            const challenge_td = document.createElement('td');
            challenge_td.textContent = data.total_solved + "/" + data.Ccount;

            // 마지막 활동 시간 표시
            const last_active_td = document.createElement('td');
            last_active_td.textContent = data.latest_date;

            card.appendChild(rank_td);
            card.appendChild(user_td);
            card.appendChild(point_td);
            card.appendChild(challenge_td);
            card.appendChild(last_active_td);

            return card;

        }

        function setScoreBoard() {

            fetch('/php/getScoreBoard.php')
                .then(response => response.json())
                .then(data => {
                    // console.log(data);

                    score_table_body_el.innerHTML = '';
                    data.forEach((item, index) => {
                        const card = createScoreCard(item, index + 1);
                        score_table_body_el.appendChild(card);
                    });


                })

        }

        list_score_list_el.addEventListener('click', () => {
            setScoreBoard();
        });


        // 최근 활동 카드 생성기
        function createActivityCard(data) {
            // 메인 카드 컨테이너
            const card = document.createElement('div');
            card.className = 'activity-card';

            // 아이콘
            const icon = document.createElement('span');
            icon.className = 'material-symbols-outlined text-success';
            icon.textContent = 'task_alt';

            // 내용을 담을 div
            const contentDiv = document.createElement('div');

            // 텍스트 내용 생성
            const content = document.createElement('div');

            // 유저 이름
            const userStrong = document.createElement('strong');
            userStrong.textContent = data.nickname;

            // 챌린지 이름
            const challengeSpan = document.createElement('span');
            challengeSpan.className = 'text-info';
            challengeSpan.textContent = data.c_title;

            // 시간 표시
            const timeDiv = document.createElement('div');
            timeDiv.className = 'text-muted small';
            timeDiv.textContent = data.d_time;

            // 요소들 조립
            content.appendChild(userStrong);
            content.appendChild(document.createTextNode(' solved '));
            content.appendChild(challengeSpan);

            contentDiv.appendChild(content);
            contentDiv.appendChild(timeDiv);

            card.appendChild(icon);
            card.appendChild(contentDiv);

            return card;
        }

        // 최근 활동 리스트 생성
        function setUserActivities() {
            try {

                fetch('/php/getUActivities.php') // PHP 파일 경로로 변경
                    .then(response => response.json())
                    .then(data => {
                        // console.log(data);
                        // 추가적인 데이터 처리...
                        activity_grid_el.innerHTML = '';
                        data.forEach(item => {
                            const card = createActivityCard(item);
                            activity_grid_el.appendChild(card);
                        });


                    })
                    .catch(error => console.error('Error:', error));

            } catch (error) {
                console.error('활동 기록 업데이트 중 오류 발생:', error);
            }


        }

        // 최근 활동 리스트 업데이트
        async function updateUserActivities() {

            setUserActivities();

        }

        // 30초 간격으로 업데이트 반복
        setInterval(updateUserActivities, 30000);


        // 메인 첼린지 카드 생성기
        function createChallengeCard(data) {
            // 메인 카드 컨테이너
            const card = document.createElement('div');
            card.className = 'challenge-card';

            // 상단 타입과 난이도 컨테이너
            const headerContainer = document.createElement('div');
            headerContainer.className = 'd-flex justify-content-between align-items-center mb-3';

            // 챌린지 타입 (WEB)
            const typeSpan = document.createElement('span');
            typeSpan.className = 'challenge-type web';
            typeSpan.textContent = 'WEB';

            // 난이도
            const difficultySpan = document.createElement('span');
            difficultySpan.className = `difficulty ${data.c_difficulty.toLowerCase()}`;
            difficultySpan.textContent = data.c_difficulty;

            // 제목
            const title = document.createElement('h3');
            title.textContent = data.c_title;

            // 설명
            const description = document.createElement('p');
            description.className = 'text-muted small mb-3';
            // description.textContent = data.c_text;
            description.textContent = "웹은 웹 페이지 고유 설명을 따릅니다.";


            // 하단 포인트와 솔브 수 컨테이너
            const footerContainer = document.createElement('div');
            footerContainer.className = 'd-flex justify-content-between';

            // 포인트
            const pointsSpan = document.createElement('span');
            pointsSpan.className = 'points';
            pointsSpan.textContent = `${data.c_point} pts`;

            // 솔브 수
            const solveCountSpan = document.createElement('span');
            solveCountSpan.className = 'solve-count';
            solveCountSpan.textContent = `Solves: ${data.c_solves}`;

            // 요소들 조립
            headerContainer.appendChild(typeSpan);
            headerContainer.appendChild(difficultySpan);

            footerContainer.appendChild(pointsSpan);
            footerContainer.appendChild(solveCountSpan);

            card.appendChild(headerContainer);
            card.appendChild(title);
            card.appendChild(description);
            card.appendChild(footerContainer);

            return card;
        }



        const init = () => {

            // 챌린지 카드 생성
            setChallenges();
            // 스코어 카드 생성
            set_sc_form();
            // 포인트 카드 생성
            getpointview();

            // 메인 통계 정보 출력
            set_main_stats();
            // 최근 활동 업데이트
            setUserActivities();


            const admin_m_el = document.querySelectorAll('.admin_m');
            let userAccess = "<?php echo $_SESSION['user_access']; ?>";
            let userRole = "<?php echo $_SESSION['user_role']; ?>";

            // console.log('userAccess=' + userAccess);
            if (userAccess == true) {
                // console.log('로그인됨');

                // 메인 데이터 출력
                set_main_data();
                // 채팅 서버 연결 시도
                connectWebSocket();

                n_name_el.style.display = 'block';
                n_point_el.style.display = 'block';
                n_ch_btn_el.style.display = 'block';
                n_login_el.style.display = 'none';
                n_logout_btn_el.style.display = 'block';


                if (userRole == 'admin') {
                    admin_m_el.forEach(item => {
                        item.classList.add('d-flex');
                        item.style.display = 'block';
                    });
                } else {
                    admin_m_el.forEach(item => {
                        item.classList.remove('d-flex');
                        item.style.display = 'none';
                    });
                }




            } else {

                n_name_el.style.display = 'none';
                n_point_el.style.display = 'none';
                n_ch_btn_el.style.display = 'none';
                n_login_el.style.display = 'block';
                n_logout_btn_el.style.display = 'none';

                admin_m_el.forEach(item => {
                    item.classList.remove('d-flex');
                    item.style.display = 'none';
                });

            }



        }



        // 채팅창 on / off
        let chaton = false;
        // const mid_body_el = document.querySelector('.mid-body');

        const chat_box_el = document.getElementById('chat-box');
        // const n_ch_btn_el = document.getElementById('n-ch-btn');
        n_ch_btn_el.addEventListener('click', function() {
            if (chaton === false) {
                chat_box_el.style.display = 'flex';
                chaton = true;
            } else {
                chat_box_el.style.display = 'none';
                chaton = false;
            }

            document.querySelector('.chat-header .btn-close').addEventListener('click', function() {
                chat_box_el.style.display = 'none';
                chaton = false;
            });



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

            window.location.href = '/php/logout.php';


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
            xhr.open('POST', '/php/idcheck.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {

                if (xhr.status === 200) {

                    let response = xhr.responseText;
                    // console.log(response);
                    if (response == "true") {

                        // console.log('중복 임')
                        is_id_check_ok('false');

                    } else if (response == "null") {

                        is_id_check_ok('null');

                    } else {

                        // console.log('중복 아님')
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



        // 페이지 무빙 제어 함수
        function initFullPageScroll(containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const sections = container.querySelectorAll('.section');
            let currentSectionIndex = 0;
            let isScrolling = false; // 빠른 스크롤 방지

            container.addEventListener('wheel', (event) => {
                if (isScrolling) return; // 스크롤이 이미 진행 중이면 무시

                isScrolling = true; // 스크롤 시작

                // 휠 내리기
                if (event.deltaY > 0) {
                    if (currentSectionIndex < sections.length - 1) {
                        currentSectionIndex++;
                    }
                } else {
                    // 휠 올리기
                    if (currentSectionIndex > 0) {
                        currentSectionIndex--;
                    }
                }

                // 섹션으로 부드럽게 이동
                sections[currentSectionIndex].scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });

                // 스크롤 완료 후, 스크롤 허용
                setTimeout(() => {
                    isScrolling = false;
                }, 1000); // 1초 간격으로 스크롤을 제어 (부드럽게 이동하기 위함)

                event.preventDefault(); // 기본 스크롤 방지
            });
        }

        // 유저 데이터 생성
        document.getElementById('list-userdata-list').addEventListener('click', () => {

            fetch('/php/getUCCountData.php') // PHP 파일 경로로 변경
                .then(response => response.json())
                .then(data => {
                    // console.log(data);
                    // 추가적인 데이터 처리...

                    // 초기화
                    const lu_fullPage_1_el = document.getElementById('lu-fullPage-1');
                    lu_fullPage_1_el.innerHTML = '';

                    const lu_fullPage_2_el = document.getElementById('lu-fullPage-2');
                    lu_fullPage_2_el.innerHTML = '';


                    // 진행도 바 애니메이션 생성기
                    function animateProgress(element, targetPercentage, duration = 1500) {
                        // 항상 0에서 시작
                        element.style.width = '0%';
                        const startTime = performance.now();

                        // targetPercentage가 100을 넘지 않도록 제한
                        targetPercentage = Math.min(targetPercentage, 100);

                        function update(currentTime) {
                            const elapsed = currentTime - startTime;
                            const progress = Math.min(elapsed / duration, 1);

                            // 0에서 목표값까지 부드럽게 증가
                            const currentWidth = progress * targetPercentage;
                            element.style.width = `${currentWidth}%`;

                            if (progress < 1) {
                                requestAnimationFrame(update);
                            }
                        }

                        requestAnimationFrame(update);
                    }






                    // 진행도 바 생성기
                    function createProgressBar(label, current, total) {
                        // 0으로 나누기 방지
                        if (!total) total = 1;

                        const percentage = (current / total) * 100;

                        // 진행도 바 라벨 생성
                        const labelEl = document.createElement('label');
                        labelEl.className = 'form-label mt-3';
                        labelEl.setAttribute('for', 'progress-bar');
                        labelEl.textContent = label;

                        // 진행도 바 컨테이너 생성
                        const progressContainer = document.createElement('div');
                        progressContainer.className = 'progress position-relative'; // position-relative 추가

                        // 진행도 바 엘리먼트 생성
                        const progressEl = document.createElement('div');
                        progressEl.className = 'progress-bar custom-progress-bar';
                        progressEl.style.width = percentage + '%';

                        // 수치 표시용 텍스트 엘리먼트 생성
                        const textEl = document.createElement('span');
                        textEl.className = 'position-absolute w-100 text-center'; // Bootstrap 클래스로 중앙 정렬
                        textEl.style.left = '0';
                        textEl.style.right = '0';
                        textEl.style.top = '50%';
                        textEl.style.transform = 'translateY(-50%)'; // 수직 중앙 정렬
                        textEl.textContent = `${current} / ${total}`;

                        // 합치기
                        progressContainer.appendChild(progressEl);
                        progressContainer.appendChild(textEl);

                        // JavaScript 애니메이션 적용
                        animateProgress(progressEl, percentage);

                        return {
                            labelEl,
                            progressEl: progressContainer // 컨테이너를 반환
                        };
                    }


                    // 라인 생성기
                    function createLineBar(label) {

                        // 라인 라벨 생성
                        const labelEl = document.createElement('label');
                        labelEl.className = 'form-label mt-3';
                        labelEl.setAttribute('for', 'line-bar');
                        labelEl.textContent = label;

                        // 진행도 바 컨테이너 생성
                        const lineBarEl = document.createElement('div');
                        lineBarEl.className = 'line-bar'
                        lineBarEl.style.borderTop = "1px solid #fff";
                        lineBarEl.style.width = "100%";
                        lineBarEl.style.height = "1px";

                        return {
                            labelEl,
                            lineBarEl
                        };
                    }

                    // 진행도 바를 생성
                    const totalProgress = createProgressBar('진행도', data.difficulty[0] + data.difficulty[1] + data.difficulty[2], data.Ccount);


                    // 메인 차트 컨테이너
                    const c_div_el = document.createElement('div');
                    c_div_el.className = "d-flex justify-content-center gap-4"; // gap-4 추가
                    c_div_el.style.height = "520px";



                    // 각 차트를 위한 개별 컨테이너
                    const d_chart_container = document.createElement('div');
                    d_chart_container.className = "d_chart-container";
                    d_chart_container.style.width = "420px"; // 컨테이너 크기 지정
                    d_chart_container.style.height = "420px"; // 컨테이너 크기 지정
                    d_chart_container.style.margin = "0 auto";

                    const p_chart_container = document.createElement('div');
                    p_chart_container.className = "p_chart-container";
                    p_chart_container.style.width = "420px"; // 컨테이너 크기 지정
                    p_chart_container.style.height = "420px"; // 컨테이너 크기 지정
                    p_chart_container.style.margin = "0 auto";
                    // 난이도 차트 생성
                    const d_chart_el = document.createElement('canvas');
                    d_chart_el.id = 'd_chart'; // ID 부여
                    // 포인트 차트 생성
                    const p_chart_el = document.createElement('canvas');
                    p_chart_el.id = 'p_chart'; // ID 부여

                    const d_chart_label_el = document.createElement('label');
                    d_chart_label_el.className = "form-label";
                    d_chart_label_el.setAttribute('for', 'd_chart');
                    d_chart_label_el.style.fontSize = "1.2rem";
                    d_chart_label_el.textContent = "난이도 분포";

                    const p_chart_label_el = document.createElement('label');
                    p_chart_label_el.className = "form-label";
                    p_chart_label_el.setAttribute('for', 'p_chart');
                    p_chart_label_el.style.fontSize = "1.2rem";
                    p_chart_label_el.textContent = "포인트 분포";

                    const none_count = data.Ccount - (data.difficulty[0] + data.difficulty[1] + data.difficulty[2]);
                    const d_chart_data = {
                        labels: ['none', 'Easy', 'Normal', 'Hard'],
                        datasets: [{
                            label: '클리어 갯수 ',
                            data: [none_count, data.difficulty[0], data.difficulty[1], data.difficulty[2]],
                            backgroundColor: [
                                'rgba(128, 128, 128, 0.5)', // none - 회색 계열
                                'rgba(0, 255, 157, 0.5)', // Easy - 네온 그린
                                'rgba(0, 195, 255, 0.5)', // Normal - 네온 블루 (#00c3ff)
                                'rgba(255, 62, 62, 0.5)' // Hard - 네온 레드
                            ],
                            borderColor: [ // 테두리 색상 추가
                                'rgba(128, 128, 128, 0.8)', // none
                                'rgba(0, 255, 157, 0.8)', // Easy
                                'rgba(0, 195, 255, 0.8)', // Normal
                                'rgba(255, 62, 62, 0.8)' // Hard
                            ],
                            borderWidth: 2, // 테두리 두께,
                            hoverOffset: 5
                        }]
                    };

                    const d_config = {
                        type: 'doughnut',
                        data: d_chart_data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                shadow: {
                                    blur: 10,
                                    color: function(context) {
                                        return context.dataset.backgroundColor[context.dataIndex];
                                    }
                                },
                                legend: {
                                    position: 'bottom', // 라벨 위치를 아래로 설정
                                    align: 'center', // 라벨 정렬
                                    labels: {
                                        padding: 20, // 라벨 간격
                                        color: '#fff', // 라벨 색상 (사이트 테마에 맞게 조정)
                                        font: {
                                            size: 22
                                        }
                                    }

                                }
                            }

                        }
                    };

                    const p_chart_data = {
                        labels: ['남은 포인트 : ' + (data.Tpoint - data.point), '획득 포인트 : ' + data.point],
                        datasets: [{
                            label: 'Point',
                            data: [data.Tpoint - data.point, data.point],
                            backgroundColor: [
                                'rgba(128, 128, 128, 0.5)', // 남은 포인트 - 회색 계열
                                'rgba(0, 195, 255, 0.5)', // 획득 포인트 - 네온 블루 (#00c3ff)
                            ],
                            borderColor: [ // 테두리 색상 추가
                                'rgba(128, 128, 128, 0.8)', // 남은 포인트
                                'rgba(0, 195, 255, 0.8)', // 획득 포인트
                            ],
                            borderWidth: 2, // 테두리 두께,
                            hoverOffset: 2
                        }]
                    };

                    const p_config = {
                        type: 'doughnut',
                        data: p_chart_data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                shadow: {
                                    blur: 10,
                                    color: function(context) {
                                        return context.dataset.backgroundColor[context.dataIndex];
                                    }
                                },
                                legend: {
                                    position: 'bottom', // 라벨 위치를 아래로 설정
                                    align: 'center', // 라벨 정렬
                                    labels: {
                                        padding: 20, // 라벨 간격
                                        color: '#fff', // 라벨 색상 (사이트 테마에 맞게 조정)
                                        font: {
                                            size: 22
                                        }
                                    }
                                }
                            }
                        }
                    };

                    // DOM에 canvas 추가 후 차트 생성
                    d_chart_container.appendChild(d_chart_label_el);
                    d_chart_container.appendChild(d_chart_el);
                    p_chart_container.appendChild(p_chart_label_el);
                    p_chart_container.appendChild(p_chart_el);

                    c_div_el.appendChild(d_chart_container);
                    c_div_el.appendChild(p_chart_container);
                    lu_fullPage_1_el.appendChild(c_div_el);

                    // 총 진행도 구분선
                    const h_div_el = createLineBar('총 진행도');

                    // DOM에 추가
                    lu_fullPage_1_el.appendChild(h_div_el.labelEl);
                    lu_fullPage_1_el.appendChild(h_div_el.lineBarEl);
                    lu_fullPage_1_el.appendChild(totalProgress.labelEl);
                    lu_fullPage_1_el.appendChild(totalProgress.progressEl);




                    // 차트 생성
                    const d_chart = new Chart(d_chart_el, d_config);
                    const p_chart = new Chart(p_chart_el, p_config);

                    // 스크롤 효과 적용
                    initFullPageScroll('lu-fullPage');

                    document.addEventListener('wheel', (event) => {

                        if (event.deltaY > 0) {
                            lu_fullPage_2_el.innerHTML = '';
                            const easyProgress = createProgressBar('Easy', data.difficulty[0], data.Ecount);
                            const normalProgress = createProgressBar('Normal', data.difficulty[1], data.Ncount);
                            const hardProgress = createProgressBar('Hard', data.difficulty[2], data.Hcount);
                            const pointProgress = createProgressBar('총 획득 포인트', data.point, data.Tpoint);

                            // 난이도 별 세부 정보 구분선
                            const d_line_el = createLineBar('난이도 별 세부 정보');
                            // 획득 포인트 정보 구분선
                            const p_line_el = createLineBar('획득 포인트 정보');

                            lu_fullPage_2_el.appendChild(d_line_el.labelEl);
                            lu_fullPage_2_el.appendChild(d_line_el.lineBarEl);
                            lu_fullPage_2_el.appendChild(easyProgress.labelEl);
                            lu_fullPage_2_el.appendChild(easyProgress.progressEl);
                            lu_fullPage_2_el.appendChild(normalProgress.labelEl);
                            lu_fullPage_2_el.appendChild(normalProgress.progressEl);
                            lu_fullPage_2_el.appendChild(hardProgress.labelEl);
                            lu_fullPage_2_el.appendChild(hardProgress.progressEl);

                            lu_fullPage_2_el.appendChild(p_line_el.labelEl);
                            lu_fullPage_2_el.appendChild(p_line_el.lineBarEl);
                            lu_fullPage_2_el.appendChild(pointProgress.labelEl);
                            lu_fullPage_2_el.appendChild(pointProgress.progressEl);

                            // 세부 베치 조절
                            hardProgress.progressEl.classList.add('mb-5');
                            pointProgress.progressEl.classList.add('mb-5');
                        }

                    });

                    // 세부 배치 조절
                    totalProgress.progressEl.classList.add('mb-5');




                })
                .catch(error => console.error('Error:', error));


        })

        // 채팅 서버 연결 및 구현 로직-----------------------------------------
        const chat_send_btn_el = document.getElementById('chat-send-btn');
        const chat_input_el = document.getElementById('chat-input');
        const chat_messages_el = document.getElementById('chat-messages');

        // WebSocket 연결 상태 관리 용 변수
        let socket = null;
        let isConnected = false;

        // WebSocket 연결 로직
        function connectWebSocket() {
            try {
                // WebSocket 연결 대상 서버
                socket = new WebSocket('ws://192.168.1.133:30000');

                // WebSocket 연결 성공 로직
                socket.onopen = function() {
                    // console.log('WebSocket 연결 성공!');
                    isConnected = true;

                    // 연결 된후 입장 알림을 위해 유저 닉네임 정보를 전송
                    const messageData = JSON.stringify({
                        type: 'user_connect',
                        point: n_point_el.textContent.split(' ')[2], // 현재 포인트
                        nickname: '<?php echo $_SESSION['nickname']; ?>', // PHP 세션의 닉네임
                    });
                    socket.send(messageData);

                    // 연결 성공 메시지 를 채팅창에 표시
                    const message = document.createElement('div');
                    message.textContent = '채팅 서버에 연결되었습니다.';
                    message.className = 'system-message';
                    chat_messages_el.appendChild(message);
                };

                // WebSocket 연결 종료 로직
                socket.onclose = function() {
                    // console.log('WebSocket 연결이 종료되었습니다.');
                    isConnected = false;

                    // 3초 후 재연결 시도
                    setTimeout(connectWebSocket, 3000);
                };

                // WebSocket 에러 로직
                socket.onerror = function(error) {
                    console.error('WebSocket 에러:', error);
                    isConnected = false;
                };

                // WebSocket 메시지 수신 로직
                socket.onmessage = function(event) {
                    // 디버깅 용 콘솔 메시지
                    // console.log('메시지 수신:', event.data);
                    const data = JSON.parse(event.data);

                    function createChatMessage(data) {
                        const chat_output_box = document.createElement('div');
                        chat_output_box.classList.add('chat-output-box');

                        if (data.type === 'systemmsg') {
                            // 시스템 메시지 (입장/퇴장)
                            const systemMsg_nickname = document.createElement('div');
                            systemMsg_nickname.classList.add('system-message-nickname');
                            systemMsg_nickname.style.color = data.color;
                            systemMsg_nickname.textContent = data.nickname;

                            const systemMsg = document.createElement('div');
                            systemMsg.textContent = data.message;
                            systemMsg.className = 'system-message';
                            chat_output_box.appendChild(systemMsg_nickname);
                            chat_output_box.appendChild(systemMsg);
                        } else {
                            // 일반 채팅 메시지
                            const chat_user_box = document.createElement('div');
                            chat_user_box.classList.add('chat-user-box');

                            const chat_nickname = document.createElement('div');
                            chat_nickname.classList.add('chat-nickname');
                            chat_nickname.style.color = data.color;
                            chat_nickname.textContent = data.nickname;

                            const chat_point = document.createElement('div');
                            chat_point.classList.add('chat-point');
                            chat_point.textContent = `(pts${data.point})`;

                            const chat_msg = document.createElement('div');
                            chat_msg.textContent = ' : ' + data.message;
                            chat_msg.className = 'chat-message';

                            chat_user_box.appendChild(chat_nickname);
                            chat_user_box.appendChild(chat_point);
                            chat_output_box.appendChild(chat_user_box);
                            chat_output_box.appendChild(chat_msg);
                        }

                        return chat_output_box;
                    }

                    // 메시지 표시 함수
                    function displayMessage(data) {
                        const messageElement = createChatMessage(data);
                        chat_messages_el.appendChild(messageElement);
                        chat_messages_el.scrollTop = chat_messages_el.scrollHeight;
                    }


                    if (data.type === 'color') {
                        // 서버에서 할당받은 색상 저장
                        myColor = data.color;
                        // console.log('할당받은 색상:', myColor);
                        return;
                    }

                    displayMessage(data);



                    if (data.type === 'user_connect') {
                        // 유저 입장 알림 표시

                        return;
                    }


                    // // 채팅창 메시지 표시 로직
                    // const chat_output_box = document.createElement('div');
                    // chat_output_box.classList.add('chat-output-box');

                    // const chat_user_box = document.createElement('div');
                    // chat_user_box.classList.add('chat-user-box');

                    // const chat_nickname = document.createElement('div');
                    // chat_nickname.classList.add('chat-nickname');
                    // chat_nickname.style.color = data.color; // 사용자별 색상 적용
                    // chat_nickname.textContent = data.nickname;

                    // const chat_point = document.createElement('div');
                    // let point = '(pts' + data.point + ') :';
                    // chat_point.classList.add('chat-point');
                    // chat_point.textContent = point;

                    // const chat_msg = document.createElement('div');
                    // chat_msg.textContent = data.message;
                    // chat_msg.className = 'chat-message';

                    // chat_user_box.appendChild(chat_nickname);
                    // chat_user_box.appendChild(chat_point);
                    // chat_output_box.appendChild(chat_user_box);
                    // chat_output_box.appendChild(chat_msg);
                    // chat_messages_el.appendChild(chat_output_box);
                    // chat_messages_el.scrollTop = chat_messages_el.scrollHeight;






                };

            } catch (error) {
                console.error('WebSocket 연결 중 에러:', error);
                // 3초 후 재연결 시도
                setTimeout(connectWebSocket, 3000);
            }
        }



        // 메시지 전송 함수
        function sendMessage() {
            if (!isConnected) {
                // console.log('WebSocket이 연결되어 있지 않습니다.');
                return;
            }

            const message = chat_input_el.value.trim();
            if (message) {
                try {
                    // 메시지 및 유저 데이터를 채팅 서버로 전송
                    const messageData = JSON.stringify({
                        message: message,
                        nickname: '<?php echo $_SESSION['nickname']; ?>', // PHP 세션의 닉네임
                        point: n_point_el.textContent.split(' ')[2] // 현재 포인트
                    });
                    socket.send(messageData);
                    // console.log('메시지 전송:', messageData);
                    chat_input_el.value = '';
                } catch (error) {
                    console.error('메시지 전송 중 에러:', error);
                }
            }
        }

        // 이벤트 리스너
        chat_send_btn_el.addEventListener('click', sendMessage);
        chat_input_el.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                sendMessage();
            }
        });

        // ----------------------------------------------------------------------



        init();
    </script>



</body>

</html>

<?php

// alert 모달 호출기 call_alert_modal
// 인덱스 = 0, 호출 여부 1, 제목 2, 내용
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