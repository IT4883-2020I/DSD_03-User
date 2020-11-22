import { apiAxios, setHeaders } from "../../../services/axios";

export const login = (body) =>
    apiAxios
        .post("login", body)
        .then((res) => {
            if (res.data.status === "successful") {
                localStorage.setItem("token", res.data.result.api_token);
                localStorage.setItem("project-type", res.data.result.type);
                setHeaders({ "token": res.data.result.api_token });
                setHeaders({ "project-type": res.data.result.type });
            }

            return res.data;
        })
        .catch((error) => {
            return error.response.data;
        });

export const register = (body) =>
    apiAxios.post("register", body).then((res) => {
        return res.data;
    });

export const forgotPassword = (body) =>
    apiAxios.post("send-email-forgot-password", body).then((res) => {
        return res.data;
    });

export const changePassword = (body) =>
    apiAxios.post("change-password", body).then((res) => {
        return res.data;
    });

export const getListUsers = (filter, type) => {
    var url = `user?page_id=${filter.page_id}&page_size=${filter.page_size}&filters=`;
    if (filter.role && filter.role != "Chưa xác định") {
        url += ",role=" + filter.role;
    }
    if (filter.status && filter.status != "Chưa xác định") {
        url += ",status=" + filter.status;
    }
    if (filter.search) {
        url += "&p=" + filter.search;
    }
    return apiAxios.get(url).then((res) => {
        return res.data;
    });
};

export const getUser = (userId) => {
    return apiAxios
        .get(`user/${userId}`)
        .then((res) => {
            return res.data;
        })
        .catch((error) => {
            return error.response.data;
        });
};

export const updateUser = (body) => {
    return apiAxios.patch(`user/${body.id}`, body).then((res) => {
        return res.data;
    });
};

export const createUser = (body) => {
    return apiAxios.post("user", body).then((res) => {
        return res.data;
    });
};

export const uploadAvatar = ({ file }) => {
    let formData = new FormData();
    formData.append("file", file);

    return apiAxios.post("upload", formData).then((res) => {
        return res.data;
    });
};
