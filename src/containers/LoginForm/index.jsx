import React, { useState } from "react";
import { Form, Input, Button, Checkbox } from "antd";
import { UserOutlined, LockOutlined } from "@ant-design/icons";
import StyleLoginForm from "./index.style";
import { Typography } from "antd";
import { withRouter } from "react-router-dom";
import { useDispatch } from "react-redux";
import { login } from "../../modules/user/store/services";
import { actions } from "../../modules/user/store";

const { Title } = Typography;

const LoginForm = ({ history }) => {
  const dispatch = useDispatch();
  const [form] = Form.useForm();

  const handleLogin = async () => {
    try {
      const res = await login(form.getFieldsValue());
      dispatch(actions.setUserData(res));
      history.push('/dashboard')
    } catch (error) {}
  };

  return (
    <StyleLoginForm>
      <Title level={2}>Hệ thống giám sát bằng Drone</Title>
      <Form
        form={form}
        name="normal_login"
        initialValues={{
          remember: true,
        }}
      >
        <Form.Item
          name="username"
          rules={[
            {
              required: true,
              message: "Please input your Username!",
            },
          ]}
        >
          <Input
            prefix={<UserOutlined className="site-form-item-icon" />}
            placeholder="Username"
          />
        </Form.Item>
        <Form.Item
          name="password"
          rules={[
            {
              required: true,
              message: "Please input your Password!",
            },
          ]}
        >
          <Input
            prefix={<LockOutlined className="site-form-item-icon" />}
            type="password"
            placeholder="Password"
          />
        </Form.Item>
        <Form.Item>
          <Form.Item name="remember" valuePropName="checked" noStyle>
            <Checkbox>Lưu tài khoản</Checkbox>
          </Form.Item>

          <a className="login-form-forgot" onClick={() => history.push("/forgot-password")}>
            Quên mật khẩu
          </a>
          <a className="login-form-forgot" onClick={() => history.push("/register")}>
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

export default withRouter(LoginForm);
