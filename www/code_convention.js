// ===========================================
// ========== CODE CONVETION
// ===========================================

// Đặt tên theo code convention:
	// Tên hàm snake_case nhưng thêm _ phía trước: Vd: (_myFunctionHere)
	// Tên biến camelCase bthuong hoặc snake case: let camelCaseVarriable = 1; demo_v = 1;

// Mỗi function phải có comment giải thích chức năng của function
// Các function phục vụ cùng 1 file sẽ được bỏ chung block code có comment là file name ở đầu => Muốn tìm code trong file nào có thể Ctrl+F theo tên file đó
// Ví dụ: 

// ==================== INDEX.PHP ==========================
// Chức năng hàm trả về số 1
function _my_func() {
	let demoRes = 1;
	return demoRes;
}

// @param: id
// Hàm trả về bình phương id
function _my_func2(id) {
	return id**2;
}

// ==================== PRODUCT.PHP ==========================
// Chức năng hàm trả về số 1
function _my_func3() {
	return 1;
}

// @param: id
// Hàm trả về bình phương id
function _my_func4(id) {
	return id**2;
}