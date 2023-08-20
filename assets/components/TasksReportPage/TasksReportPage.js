import React, {useLayoutEffect, useState} from 'react';
import PanelHeading from "../App/PanelHeading/PanelHeading";
import Helper from "../App/Helper";
import Config from "../App/Config";
import Page from "../App/Page";
import PanelBody from "../App/PanelBody/PanelBody";
import Icon from "../App/Common/Icon";
import LocalStorage from "../App/LocalStorage";
import TaskGithubLink from "../TasksPage/Task/TaskHeader/TaskGithubLink/TaskGithubLink";
import './TasksReportPage.scss';

const TasksReportPage = () => {
  const title = "Report";
  const icon = <Icon name="list-alt"/>;

  const [tasks, setTasks] = useState([]);
  const [search, setSearch] = useState("");
  const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());

  const events = new function () {
    return {
      init: () => {
        Helper.fetchJson(Config.apiUrlPrefix + "/tasks/status/progress")
          .then(response => {
            setTasks(response.tasks);
            setReminderNumber(response.reminderNumber);
            LocalStorage.setReminderNumber(reminderNumber);
          });
      },
      onSearchUpdate: () => {}
    }
  }

  useLayoutEffect(events.init, []);
  useLayoutEffect(events.onSearchUpdate, [search]);

  return (
    <Page sidebar={{root: null, onSearch: setSearch, reminderNumber: reminderNumber}}>
      <PanelHeading title={title} icon={icon}/>
      <PanelBody>
        <ul class="tasks-report-list">
          {tasks.map(task => <li><TaskGithubLink link={task.link} /> {task.title}</li>)}
        </ul>
      </PanelBody>
    </Page>
  );
}

export default TasksReportPage;
