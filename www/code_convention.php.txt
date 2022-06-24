<!-- 
Cây thư mục:
Chia làm các package chính:

index.php
View
-------User
--------------Login.php
--------------UserInfo.php
--------------UserManagement.php
-------Product
--------------ProductsList.php
--------------ProductDetail.php
	      ....
-------Utils
--------------Search.php
BackEnd/API
-------User
	      ...
-------Product
	      ...

// Sử dụng "" để trích dẫn các chuỗi text, các hàm echo thì dùng '' để bao ngoài
echo '<a href="/static/link" title="Hello!">Link name</a>';
echo '<a href="$link" title="$link_title">$link_name</a>';

// Mỗi khi fetch dữ liệu, mỗi một thuộc tính đều phải xuống dòng

$query = new WP_Query( array( 'ID' => 123 ) );

$args = array(
[tab]'post_type'   => 'page',
[tab]'post_author' => 123,
[tab]'post_status' => 'publish',
);
 
$query = new WP_Query( $args );

// Format các câu lệnh if else
// Use elseif not else if
if ( condition ) {
    action1();
    action2();
} elseif ( condition2 && condition3 ) {
    action3();
    action4();
} else {
    defaultaction();
}
 -->
