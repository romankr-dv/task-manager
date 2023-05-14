import React from 'react';
import OpenIcon from "../../../../App/Common/OpenIcon";

const TaskPanelViewButton = ({task, events}) => {
  const onButtonClick = () => events.updateTaskControlPanelView(task.id, !task.isTaskControlPanelOpen);
  const iconName = task.isTaskControlPanelOpen ? "chevron-top" : "chevron-bottom";
  return (
    <button onClick={onButtonClick} className='title-button'>
      <OpenIcon name={iconName}/>
    </button>
  );
}

export default TaskPanelViewButton;
