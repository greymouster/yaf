// 如果是在APP里面则隐藏顶部的导航栏
function handleNavigationBar() {
    if (isAPP()) {
        document.getElementsByClassName("top")[0].style.height = "0";
    }
    else {
        document.getElementsByClassName("top")[0].style.height = "45px";
    }
}

// 判断是否在APP里面
function isAPP() {
    var flag = false;
    try {
        flag = window.Showboom.isAPP();
    }
    catch (error) {/* You can handle exception here. */}
    return flag;
}
