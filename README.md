# Dev SQL Tool

一个本地演示项目，用于提供管理员可用的只读 SQL 查询工具。

核心能力：

- 仅允许执行 `SELECT`
- 支持分页
- 支持 JSON / Excel 导出
- 显示数据库错误信息
- 记录 SQL 审计日志

## 技术栈

- Laravel 10
- Laravel Breeze
- Tailwind CSS + Vite
- MySQL
- `spatie/laravel-permission`
- `maatwebsite/excel`

## 快速启动

推荐环境：

- PHP 8.2+
- Composer 2.x
- Node.js 20 LTS / 22 LTS
- npm 10+
- MySQL 8.0（5.7+ 兼容）

本地验证环境：

- PHP 8.4
- Node.js 25.6.1
- MySQL 5.7

启动步骤：

```bash
composer install
npm install
cp .env.example .env
mysql -uroot -p -e "CREATE DATABASE IF NOT EXISTS dev_sql_tool CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

如果你的本地 MySQL 用户名或密码不同，请先修改 `.env` 中的数据库配置。

启动后访问：

- 首页：`http://127.0.0.1:8000/`
- 登录页：`http://127.0.0.1:8000/login`
- SQL 工具页：`http://127.0.0.1:8000/dev`

如果需要前端热更新，可以使用：

```bash
npm run dev
```

## 默认账号

仅适用于本地种子数据。

管理员账号：

- Email：`admin@qq.com`
- Password：`password`

测试用户：

- 共 `100` 个
- 邮箱范围：`test-user-001@example.com` 到 `test-user-100@example.com`
- 密码统一为：`password`

## 快速验证

1. 使用管理员账号登录并访问 `/dev`
2. 执行：

```sql
select * from users order by id asc
```

预期：

- 页面正常显示结果
- 地址栏为 `/dev?execution_id=...`
- 支持分页

3. 执行：

```sql
update users set name = 'x' where id = 1
```

预期：

- 被拒绝
- 页面显示错误信息

4. 执行：

```sql
select * from not_exists_table
```

预期：

- 页面显示数据库错误

5. 点击导出按钮

预期：

- JSON 可以下载
- Excel 可以下载

## 注意事项

- `/dev` 设计目标是只读查询工具，不是通用数据库管理工具
- 如果接 MySQL，优先使用只读账号
- 导出会重新执行完整 SQL，不是导出当前页 50 条结果
- 当前结果集上限为 `5000` 行
- `/dev` 主链路目前以手动验证为主
