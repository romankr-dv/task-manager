import {useLocation, useParams} from "react-router-dom";
import Config from "./Config";

const Helper = new function () {
  const timeoutStorage = {};
  return {
    addTimeout: (id, func, timeout) => {
      clearTimeout(timeoutStorage[id]);
      timeoutStorage[id] = setTimeout(func, timeout);
    },
    fetch: (url, options) => {
      return fetch(url, options).then(response => {
        // used to fix logout redirect
        if (response.redirected) {
          location.reload();
        }
        return response;
      });
    },
    fetchJson: (url, params = null) => {
      if (params) {
        // json parse used for removing undefined fields
        params = JSON.parse(JSON.stringify(params));
        url += '?' + new URLSearchParams(params);
      }
      return Helper.fetch(url).then(response => response.json());
    },
    fetchJsonPost: (url, body) => {
      return Helper.fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(body)
      }).then(response => response.json());
    },
    getTaskPageUrl(taskId) {
      const params = useParams();
      const location = useLocation();
      let url = location.pathname;
      if (params.root) {
        const parts = url.split('/');
        parts.shift();
        parts.shift();
        url = '/' + parts.join('/');
      }
      if (taskId) {
        url = '/' + taskId + url;
      }
      return url;
    },
    fetchNewTask(parent) {
      const url = Config.apiUrlPrefix + '/tasks/new';
      return Helper.fetchJsonPost(url, {'parent': parent});
    },
    fetchTaskStart(taskId) {
      const url = Config.apiUrlPrefix + '/tasks/' + taskId + '/start';
      return Helper.fetchJsonPost(url);
    },
    fetchTaskFinish(taskId) {
      const url = Config.apiUrlPrefix + '/tasks/' + taskId + '/finish';
      return Helper.fetchJsonPost(url);
    },
    fetchTaskEdit(taskId, params) {
      const url = Config.apiUrlPrefix + '/tasks/' + taskId + '/edit';
      return Helper.fetchJsonPost(url, params)
    },
    fetchTaskDelete(taskId) {
      const url = Config.apiUrlPrefix + '/tasks/' + taskId + '/delete';
      return Helper.fetchJsonPost(url);
    },
    fetchHistory(params) {
      const url = Config.apiUrlPrefix + "/history";
      return Helper.fetchJson(url, params);
    },
    isGithubLink(link) {
      return link.includes(Config.githubUrlPrefix)
    },
    getGithubIssueNumber(link) {
      return "#" + link.split('/').slice(-1)[0];
    },
    getHistoryPageUrl(task) {
      return (task ? '/' + task.id : '') + '/history';
    }
  }
}

export default Helper;
