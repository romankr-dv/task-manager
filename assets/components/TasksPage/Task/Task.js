import React from 'react';
import TaskHeader from "./TaskHeader/TaskHeader";
import TaskControlPanel from "./TaskControlPanel/TaskControlPanel";
import TaskStatusBadge from "./TaskStatusBadge/TaskStatusBadge";
import moment from "moment";
import './Task.scss';

const Task = ({task, data, events}) => {
  const {statuses} = data;
  const isReminder = task.reminder && task.reminder < moment().unix();
  const status = statuses.find((status) => status.id === task.status);

  return (
    <div className="task">
      <TaskStatusBadge isReminder={isReminder} status={status}/>
      <TaskHeader task={task} events={events}/>
      {task.isTaskControlPanelOpen ? <TaskControlPanel task={task} statuses={statuses} events={events}/> : null}
    </div>
  )
}

export default Task;
