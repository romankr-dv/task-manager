import React from 'react';
import './TaskHeader.scss';
import TaskPanelViewButton from "./TaskPanelViewButton/TaskPanelViewButton";
import TaskTitle from "./TaskTitle/TaskTitle";
import TaskLink from "./TaskLink/TaskLink";
import TaskPageButton from "./TaskPageButton/TaskPageButton";
import TaskGithubLink from "./TaskGithubLink/TaskGithubLink";

const TaskHeader = ({task, events}) => {
  return (
    <div className="task-header">
      {task.link ? <TaskGithubLink link={task.link}/> : null}
      <TaskTitle task={task} events={events}/>
      {task.link ? <TaskLink link={task.link}/> : null}
      <TaskPageButton task={task}/>
      <TaskPanelViewButton task={task} events={events}/>
    </div>
  )
}

export default TaskHeader;
