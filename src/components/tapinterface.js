const endpoint = "https://www.tmysam.top/caseManager/apis/";
const ssoServiceEndpoint = "https://account.tmysam.top/apis/";

export default class tapi {
    check_existance(caseId) {
        return new Promise((resolve, reject) => {
            fetch(endpoint + "check_existance.php?caseId=" + caseId)
                .then((response) => {
                    response.json().then((data) => {
                        resolve(data);
                    });
                })
                .catch((err) => {
                    reject(err);
                });
        });
    }
    get_info(caseId) {
        return new Promise((resolve, reject) => {
            fetch(endpoint + "get_case_data.php?caseId=" + caseId)
                .then((response) => {
                    response.json().then((data) => {
                        resolve(data);
                    });
                })
                .catch((err) => {
                    reject(err);
                });
        });
    }
    new_incident(caseId, text) {
        text = encodeURIComponent(text);
        return new Promise((resolve, reject) => {
            fetch(endpoint + "new_incident.php", {
                method: "POST",
                body: "caseId=" + caseId + "&text=" + text,
                credentials: "include",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
            })
                .then((response) => {
                    response.json().then((data) => {
                        resolve(data);
                    });
                })
                .catch((err) => {
                    reject(err);
                });
        });
    }
    delete_incident(dataId) {
        return new Promise((resolve, reject) => {
            fetch(endpoint + "delete_incident.php", {
                method: "POST",
                body: "dataId=" + dataId,
                credentials: "include",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
            })
                .then((response) => {
                    response.json().then((data) => {
                        resolve(data);
                    });
                })
                .catch((err) => {
                    reject(err);
                });
        });
    }
    checkPermission(permissionName) {
        let url =
            ssoServiceEndpoint +
            "sso-interface.php?operation=6&permission=" +
            permissionName;
        return new Promise((resolve, reject) => {
            fetch(url, { credentials: "include" })
                .then((response) => {
                    return response.json();
                })
                .then((data) => {
                    if (data.status == 0 && data.result == 1) {
                        resolve({ permission: true });
                    } else {
                        if (data.status != 0) {
                            resolve({
                                permission: false,
                                noPermissionReason:
                                    "未登录或邮件未验证，请在 TSStudio UAS(Universal Authentication System) 登录并验证邮件后重试。",
                            });
                        } else {
                            resolve({
                                permission: false,
                                noPermissionReason:
                                    "您没有权限。这要求您在权限节点：" +
                                    permissionName +
                                    "上有值为1的权限。",
                            });
                        }
                    }
                })
                .catch((error) => {
                    reject(error);
                });
        });
    }
}
