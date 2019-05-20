layui.define(['lodash', 'axios'], function(exports) {
  var _ = layui.lodash,
    axios = layui.axios;
  var utils = {
    error: function(msg) {
      console.error(msg);
    },
    oneOf: function(value, validList) {
      var flag = false;
      _.forEach(validList, function(item, index) {
        if (item === value) {
          flag = true;
        }
      });
      return flag;
    },
    // 本地存储相关
    localStorage: {
      getItem: function(key) {
        return JSON.parse(localStorage.getItem(key));
      },
      setItem: function(key, data) {
        var d = (typeof data === 'object' || typeof data === 'array') ?
          JSON.stringify(data) : data;
        localStorage.setItem(key, d);
      },
      removeItem: function(key) {
        localStorage.removeItem(key);
      },
      clear: function() {
        localStorage.clear();
      }
    },
    /**
     * 在一个数组里面查询一个对象
     * var r = 1;
     * var arr = [{name:'a',id:1},{name:'b',id:2}]
     * var result = utils.find(arr,function(item){
     *   return r === item.id;
     * });
     *  // result : {name:'a',id:1}
     */
    find: function(arr, callback) {
      return arr[_.findKey(arr, callback)];
    },
    /*
    * 动态地址分析
    * @param string hash    当前地址
    * @param array routes    全部路由
    * */
      match:function(hash,routes) {

          var hash_arr = hash.split('/');       //hash_arr    数组当当前地址
          var count_match = 0;                  //count_match     通配符数量
          var str_hash ='';                     //str_hash    非通配符部分地址
          var match_hash = '';                  //match_hash  通配符部分地址
          var sys_hash = '';                    //sys_hash      系统路由地址

          //对路由总地址进行循环
          for(var pi = 0 ; pi < routes.length ; pi++) {
              var routes_arr = routes[pi].path.split('/');

              if(hash_arr.length === routes_arr.length && hash_arr[1] === routes_arr[1]){

                  //统计出现通用符 * 的次数
                  for(var li = 0 ; li < routes_arr.length ; li++) {
                      if (routes_arr[li] === '*') {
                          count_match += 1;
                      }
                  }

                  /*
                   * 循环判断去除通用符部分，是否同时
                   * var str_hash 去除去除通用符部分路由
                   * */
                  for(var ni = 0 ; ni < routes_arr.length-count_match ; ni++) {
                      if(hash_arr[ni] === routes_arr[ni]) {
                          str_hash += hash_arr[ni]+'/';
                      }else{
                          str_hash = '';
                          count_match = 0;
                      }
                  }

                  /*
                   * 判断 var str_hash 是否为空
                   * 为空     未匹配到路由
                   * 不为空   匹配到路由
                   * return  匹配并拼接的路由地址
                   * */
                  if(str_hash != ''){
                      for(var hi = 0 ; hi < count_match ; hi++) {
                          str_hash += hash_arr[hash_arr.length-count_match+hi]+'/';
                          match_hash += hash_arr[hash_arr.length-count_match+hi]+'/';
                      }

                      sys_hash = routes[pi].component.substring(0,routes[pi].component.indexOf(":"))+match_hash;
                      sys_hash = sys_hash.substring(0,sys_hash.length-1);
                      str_hash = str_hash.substring(0,str_hash.length-1);
                     /* var match_position = routes[pi].component.indexOf(":");
                      if(match_position > 1) {
                          sys_hash = routes[pi].component.substring(0,match_position)+match_hash;
                          sys_hash = sys_hash.substring(0,sys_hash.length-1);
                      }else{
                          sys_hash = routes[pi].component;
                      }*/
                      routes[pi].component = sys_hash;
                      routes[pi].path = str_hash;
                      return routes[pi];
                  }
              }
          }
          return false;
      },

    // 读取模板
    tplLoader: function(url, callback, onerror) {

      var that = this;
      var data = '';
      // TODO 跨域未实现
      axios.get(url + '?v=' + new Date().getTime())
        .then(function(res) {
          data = res.data;
          var regList = [];
          // 重置id 防止冲突
          var ids = data.match(/id=\"\w*\"/g);
          ids !== null && _.forEach(ids, function(item) { regList.push(item); });
          // 重置lay-filter 防止冲突
          var filters = data.match(/lay-filter=\"\w*\"/g);
          filters !== null && _.forEach(filters, function(item) { regList.push(item); });

          if (regList.length > 0) {
            // 循环替换
            _.forEach(regList, function(item) {
              var matchResult = item.match(/\"\w*\"/);
              if (matchResult !== undefined && matchResult != null && matchResult.length > 0) {
                var result = matchResult[0];
                var regStr = result.substring(1, result.length - 1);
                var reg = new RegExp(regStr, 'g');
                data = data.replace(reg, that.randomCode());
              }
            });
          }
        })
        .catch(function(error) {
          var request = error.request;
          var errorMsg = '读取模板出现异常，异常代码：' + request.status + '、 异常信息：' + request.statusText;
          console.log(errorMsg);
          typeof onerror === 'function' && onerror(errorMsg);
        });

      var interval = setInterval(function() {
        if (data !== '') {
          clearInterval(interval);
          callback(data);
        }
      }, 50);
    },
    setUrlState: function(title, url) {
      history.pushState({}, title, url);
    },
    // 获取随机字符
    randomCode: function() {
      return 'r' + Math.random().toString(36).substr(2);
    },
    isFunction: function(obj) {
      return typeof obj === 'function';
    },
    isString: function(obj) {
      return typeof obj === 'string';
    },
    isObject: function(obj) {
      return typeof obj === 'object';
    }
  };
  //输出utils接口
  exports('utils', utils);
});