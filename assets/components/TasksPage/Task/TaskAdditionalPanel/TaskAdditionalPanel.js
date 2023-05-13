import React from 'react';
import TaskStatusField from "./TaskStatusField/TaskStatusField";
import TaskLinkField from "./TaskLinkField/TaskLinkField";
import TaskReminderField from "./TaskReminderField/TaskReminderField";
import './TaskAdditionalPanel.scss';
import moment from "moment";
import TaskDescriptionEditor from "./TaskDescriptionEditor/TaskDescriptionEditor";
import Button from "../../../App/Button";
import OpenIcon from "../../../App/OpenIcon";
import {Link} from "react-router-dom";
import Helper from "../../../App/Helper";

const TaskAdditionalPanel = ({task, statuses, events}) => {
  const onNewTaskClick = () => events.createNewTask(task.id);
  const onRemoveTaskClick = () => events.removeTask(task.id);
  const createdAt = moment.unix(task.createdAt).format('DD/MM/YYYY HH:mm');

  return (
    <div className="additional-panel">
      <div className="fields">
        <TaskStatusField task={task} statuses={statuses} events={events}/>
        <TaskReminderField task={task} events={events}/>
        <TaskLinkField task={task} events={events}>
          <span className="created-at">{createdAt}</span>
          <Button onClick={onNewTaskClick} tooltip="Add sub task"><OpenIcon name="plus"/></Button>
          <Link to={Helper.getHistoryPageUrl(task)}>
            <Button tooltip="View history"><OpenIcon name="clock"/></Button>
          </Link>
          <Button onClick={onRemoveTaskClick} tooltip="Remove task"><OpenIcon name="trash"/></Button>
        </TaskLinkField>
      </div>
      <TaskDescriptionEditor task={task} events={events}/>
    </div>
  );
}

export default TaskAdditionalPanel;
