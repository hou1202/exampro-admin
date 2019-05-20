var mods = [
  'element', 'sidebar', 'mockjs', 'select',
  'tabs', 'menu', 'route', 'utils', 'component', 'kit'
];

layui.define(mods, function(exports) {

    var element = layui.element,
    utils = layui.utils,
    $ = layui.jquery,
    _ = layui.lodash,
    route = layui.route,
    tabs = layui.tabs,
    layer = layui.layer,
    menu = layui.menu,
    component = layui.component,
    kit = layui.kit;


  var Admin = function() {
    this.config = {
      elem: '#app',
      loadType: 'SPA'
    };
    this.version = '1.0.0';
  };

  Admin.prototype.ready = function(callback) {
    var that = this,
      config = that.config;

    // 初始化加载方式
    var getItem = utils.localStorage.getItem;
    var setting = getItem("KITADMIN_SETTING_LOADTYPE");
    if (setting !== null && setting.loadType !== undefined) {
      config.loadType = setting.loadType;
    }

    kit.set({
      type: config.loadType
    }).init();

    // 初始化路由
    _private.routeInit(config);
    // 初始化选项卡
    if (config.loadType === 'TABS') {
      _private.tabsInit();
    }
    // 初始化左侧菜单   -- 请先初始化选项卡再初始化菜单
    _private.menuInit(config);
    // 跳转至首页
    if (location.hash === '') {
      utils.setUrlState('主页', '#/');
    }


    /**
     * 处理顶部侧滑弹出sidebar，两种处理方式
     * 一：直接通过LAYUI渲染的方式处理
     * 二：通过click事件，再渲染处理
    * */
    // 方式一：
    /*layui.sidebar.render({
      elem: '#ccleft',
      content:'p j g wh ',
      title: '这是左侧打开的栗子',
      shade: true,
      // shadeClose:false,
      direction: 'left',
      dynamicRender: true,
      url: 'table_two',
      width: '50%', //可以设置百分比和px
    });*/
    //方法二：
    /*$('#cc').on('click', function() {
      var that = this;
      layui.sidebar.render({
        elem: that,
        //content:'',
        title: '这是表单盒子',
        shade: true,
        // shadeClose:false,
        // direction: 'left'
        dynamicRender: true,
        url: 'views/form/index.html',
        width: '50%', //可以设置百分比和px
      });
    });*/

    // 监听头部右侧 nav
    component.on('nav(header_right)', function(_that) {
      var target = _that.elem.attr('kit-target');
      if (target === 'setting') {
        // 绑定sidebar
        layui.sidebar.render({
          elem: _that.elem,
          //content:'', 
          title: '设置',
          shade: true,
          // shadeClose:false,
          // direction: 'left'
          dynamicRender: true,
          url: 'setting',
          // width: '50%', //可以设置百分比和px
        });
      }
      if (target === 'help') {
        layer.alert('AOZOM客户服务信息：574137491');
      }
      if(target === 'cleft') {
        layer.alert('可能通过admin.js中的【layui.sidebar.render】方式来进行渲染页面进行侧边弹出,使用渲染弹出时，需要设置 id;alert时需设置kit-target');
      }
    });

    // 注入mock
    //layui.mockjs.inject(APIs);

    // 初始化渲染
    if (config.loadType === 'SPA') {
      route.render();
    }

    // 执行回调函数
    typeof callback === 'function' && callback();

  };
  //路由参数项
  var _private = {
    routeInit: function(config) {
      var that = this;
      // 配置路由
      var opt;
      $.ajaxSetup({async : false});
      $.post('/aoogi/opts','',function(res){
          opt = res;
      });
      var routeOpts = {
         routes:opt
      };

      /*var routeOpts = {
        //name: 'kitadmin',

        routes: [
        {
          path: '/',
          component: '/main',
          name: '主页'
        },{
          path:'/adminer',
          component:'/adminer',
          name:'管理员设置'
        },{
            path:'/adminer/create',
            component:'/adminer/create',
            name:'添加管理员'
        },{
            path:'/adminer',
            component:'/adminer',
            name:'保存管理员'
        },{
            path:'/adminer/edit/!*',
            component:'/adminer/edit/:id',
            name:'编辑管理员'
        },{
            path:'/adminer/!*',
            component:'/adminer/:id',
            name:'查看管理员'
        },{
            path:'/router',
            component:'/router',
            name:'路由设置'
        },{
            path:'/router/create',
            component:'/router/create',
            name:'添加路由'
        },{
            path:'/router/!*',
            component:'/router/:id',
            name:'查看路由'
        },{
            path:'/router/edit/!*',
            component:'/router/edit/:id',
            name:'编辑路由'
        },{
            path:'/router/!*',
            component:'router/:id',
            name:'保存路由'
        },{
            path:'/permission',
            component:'/permission',
            name:'权限设置'
        },{
            path:'/permission/create',
            component:'/permission/create',
            name:'添加权限组'
        },{
            path:'/permission/edit/!*',
            component:'/permission/edit/:id',
            name:'编辑权限组'
        },{
            path:'/config',
            component:'/config',
            name:'参数设置'
        },{
            path:'/config/create',
            component:'/config/create',
            name:'参数设置'
        },
      ]
      };*/

      if (config.loadType === 'TABS') {
        routeOpts.onChanged = function() {
          // 如果当前hash不存在选项卡列表中
          if (!tabs.existsByPath(location.hash)) {
            // 新增一个选项卡
            that.addTab(location.hash, new Date().getTime());
          } else {
            // 切换到已存在的选项卡
            tabs.switchByPath(location.hash);
          }
        }
      }
      // 设置路由
      route.setRoutes(routeOpts);
      return this;
    },
      //添加选项卡
    addTab: function(href, layid) {
      var r = route.getRoute(href);
      if (r) {
        tabs.add({
          id: layid,
          title: r.name,
          path: href,
          component: r.component,
          rendered: false,
          icon: '&#xe62e;'
        });
      }
    },
      //动态渲染配置左侧导航菜单
    menuInit: function(config) {
      var that = this;
      // 配置menu
      menu.set({
        dynamicRender: true,
        isJump: config.loadType === 'SPA',
        onClicked: function(obj) {
          if (config.loadType === 'TABS') {
            if (!obj.hasChild) {
              var data = obj.data;
              var href = data.href;
              var layid = data.layid;
              that.addTab(href, layid);
            }
          }
        },
        elem: '#menu-box',
        remote: {
          url: '/aoogi/menu',
          method: 'get'
        },
        cached: false,
        cacheKey:'AOZOMADMINMENU'
      }).render();
      return that;
    },
    tabsInit: function() {
      tabs.set({
        onChanged: function(layid) {
          // var tab = tabs.get(layid);
          // if (tab !== null) {
          //   utils.setUrlState(tab.title, tab.path);
          // }
        }
      }).render(function(obj) {
        // 如果只有首页的选项卡
        if (obj.isIndex) {
          route.render('#/');
        }
      });
    }
  }

  var admin = new Admin();
  admin.ready(function() {
    console.log('Init successed.');
  });

  //输出admin接口
  exports('admin', {});
});