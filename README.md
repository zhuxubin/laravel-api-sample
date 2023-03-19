## Laravel9.x的API后端代码

- 基于Laravel Sanctum实现授权功能
- 实现了基础的CRUD功能+文件上传功能（用户模块、专辑模块和图片管理模块）

## 主要数据表结构

![table.png](doc%2Ftable.png)

## API文档（将文件中json导入postman等工具即可使用）

![api.png](doc%2Fapi.png)

## 使用指南

```text
1、下载此仓库代码
2、cp .env.example .env && php artisan key:generate
3、安装依赖：php artisan install 
4、填充完成 .env文件中的数据库配置。完成数据库文件迁移：php artisan migrate
5、后续接入nginx 或 php artisan serve，至此项目已正常启动 
```
