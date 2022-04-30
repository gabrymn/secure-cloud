const valForm = (QIDP1, QIDP2) => {
    if ($(QIDP1).val() !== $(QIDP2).val()){
        alert("Passwords does not match");
        return false;
    }
}