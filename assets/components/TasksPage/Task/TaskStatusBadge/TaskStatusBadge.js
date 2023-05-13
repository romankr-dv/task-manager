import React from 'react';
import './TaskStatusBadge.scss';
import Config from "../../../App/Config";

const TaskStatusBadge = ({isReminder, status}) => {
  let color = status.color;
  if (isReminder) color = Config.reminderTaskColor;
  return (
    <div className="status-badge">
      <div style={{borderTopColor: color}}/>
    </div>
  )
}

export default TaskStatusBadge;
