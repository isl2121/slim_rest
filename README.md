
### [URL] 유저 관리
	1. [POST]  http://127.0.0.1/api/user/main      회원가입
	2. [PUT]   http://127.0.0.1/api/user/main      회원정보 수정
	3. [POST]  http://127.0.0.1/api/user/login     로그인
	4. [GET]   http://127.0.0.1/api/user/logout    로그아웃
 
### [URL] 게시물 관리
	1. [GET]   http://127.0.0.1/api/board/list/1   게시물 목록
	2. [GET]   http://127.0.0.1/api/board/1        게시물 상세
	3. [POST]  http://127.0.0.1/api/board/main     게시물 등록
	4. [PUT]   http://127.0.0.1/api/board/1        게시물 수정
	5. [DELETE]http://127.0.0.1/api/board/1        게시물 삭제
	
* 데이터베이스 마이그레이션
    ```
    /app/models/migrate/USER_DB_MIGRATION    회원정보
    /app/models/migrate/BOARD_DB_MIGRATION   게시물
    ```



* * *
# 테스트 환경 POSTMAN
![테스트환경](https://user-images.githubusercontent.com/11622241/56328083-4e79e980-61b8-11e9-8c17-af1e137f8e8a.png)

##### 테스트시 주의사항 put 을 사용할경우 body에서 form-data 가 아닌 x-www-form-urlencoded 로 설정하고 테스트해야함
##### 참고 : https://stackoverflow.com/a/26752693

##### 테스트시 src/config.php -> $settings['settings']['db'] 정보 수정후 진행요청


### 참고중인 동영상 강의		https://www.youtube.com/playlist?list=PLiVYpIYuvFOUDQPZ2fZdVvk1VYrbgmq67
### 나중에 추가 공부해야할 		URL https://restapitutorial.com/httpstatuscodes.html
