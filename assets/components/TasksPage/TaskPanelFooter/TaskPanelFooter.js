import React from 'react';
import './TaskPanelFooter.scss';

const TaskPanelFooter = ({tasks}) => {
  let amount = tasks ? tasks.filter(task => !task.isHidden).length : 0;
  if (!amount) {
    return null;
  }
  return (
    <div className='task-amount'>Tasks Amount: {amount}</div>
  );
}

export default TaskPanelFooter;
