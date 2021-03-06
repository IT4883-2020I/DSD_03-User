import React, { useCallback, useEffect, useState } from "react";
import { Form, Input, Button, Checkbox } from "antd";
import { UserOutlined, LockOutlined } from "@ant-design/icons";
import StyleLoginForm from "./index.style";
import { Typography } from "antd";
import { useHistory, useLocation } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { login } from "../../../store/services";
import { actions } from "../../../store";

const { Title } = Typography;

const LoginForm = () => {
    const dispatch = useDispatch();
    const [userInfo, setUserInfo] = useState({
        username: "",
        password: "",
    });
    const [message, setMessage] = useState("");

    const history = useHistory();
    const location = useLocation();
    const [saveInfo, setSaveInfo] = useState(false);
    const user = useSelector((state) => state.user.user);

    useEffect(() => {
        if (user && user.id) {
        history.push("/dashboard");
        }
    }, []);

    useEffect(() => {
        if (location.state && location.state.message) {
            setMessage(location.state.message);
        }
        const timer = setTimeout(() => {
            setMessage("");
        }, 3000);
        return () => clearTimeout(timer);
    }, [location]);

    const handleLogin = async () => {
        try {
            if (!validateData()) {
                return;
            }
            const dataLogin = {
                username: userInfo.username,
                password: userInfo.password,
            };
            const res = await login(dataLogin);
            console.log(res);
            if (res.status == "successful") {
                dispatch(actions.setUserData(res.result));
                dispatch(actions.setProjectType(res.result.type));
                history.push("/dashboard");
            } else {
                setMessage(res.message);
            }
        } catch (error) {}
    };

    const validateData = useCallback(() => {
        var retval = true;
        if (typeof userInfo.username === "undefined" || userInfo.username === "") {
            retval = false;
            setMessage("Vui lòng nhập tài khoản!");
            return retval;
        }
        if (typeof userInfo.password === "undefined" || userInfo.password === "") {
            retval = false;
            setMessage("Vui lòng nhập mật khẩu!");
            return retval;
        }
        return retval;
    }, [userInfo]);

    return (
        <StyleLoginForm>
            <Title level={2}>Hệ thống giám sát bằng Drone</Title>
            <h2>Đăng nhập</h2>
        <Form
            name="normal_login"
            initialValues={{
            remember: true,
            }}
        >
            <Form.Item name="username">
            <Input
                prefix={<UserOutlined className="site-form-item-icon" />}
                placeholder="Email hoặc sdt"
                value={userInfo.username}
                onChange={(e) =>
                setUserInfo({ ...userInfo, username: e.target.value })
                }
            />
            </Form.Item>
            <Form.Item name="password">
                <Input
                    prefix={<LockOutlined className="site-form-item-icon" />}
                    type="password"
                    placeholder="Mật khẩu"
                    value={userInfo.password}
                    onChange={(e) =>
                        setUserInfo({ ...userInfo, password: e.target.value })
                    }
                />
            </Form.Item>
            {message && <p className="noti-message">{message}</p>}
            <Form.Item>
                <Form.Item name="remember" valuePropName="checked" noStyle>
                    <Checkbox
                        onChange={(e) => setSaveInfo(e.target.value)}
                        value={saveInfo}
                        >
                        Lưu tài khoản
                    </Checkbox>
                </Form.Item>

                <a className="first-button" onClick={() => history.push("/forgot-password")}>
                    Quên mật khẩu
                </a>
                <a className="second-button" onClick={() => history.push("/register")}>
                    Đăng ký
                </a>
            </Form.Item>

                <Form.Item>
                    <Button
                        type="primary"
                        htmlType="submit"
                        className="login-form-button"
                        onClick={handleLogin}
                    >
                        Đăng nhập
                    </Button>
                </Form.Item>
            </Form>
        </StyleLoginForm>
    );
};

export default LoginForm;
