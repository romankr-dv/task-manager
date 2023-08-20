import React from 'react';
import OpenIcon from "../../../../App/Common/OpenIcon";

const TaskPriorityUpdateButton = ({task, events}) => {
  const onPriorityUpdate = () => events.updateTaskPriority(task.id)
  return (
    <button onClick={onPriorityUpdate} className='title-button hidden-title-button'>
      <OpenIcon name="data-transfer-upload"/>
    </button>
  );
}

export default TaskPriorityUpdateButton;
