AOZOM后台管理系统

**1、验证**
    后台用户的验证分为两部分，一部分为用户登录状态验证，别一部分为用户权限验证。
    验证方式为通过中间键 Authority 来进行验证。
    
    后台公共类：
    User    状态验证
    Auth    权限验证