import React from 'react';
import Button from "../../../../App/Button";
import OpenIcon from "../../../../App/OpenIcon";

const TaskTimeTrackingButton = ({task, isActive, events}) => {
  let onFinish = () => events.finishTask(task.id);
  let onStart = () => events.startTask(task.id);
  return isActive
    ? <Button onClick={onFinish} buttonStyle='info' buttonSize='sm'><OpenIcon name="media-pause"/></Button>
    : <Button onClick={onStart} buttonSize='sm'><OpenIcon name="media-play"/></Button>
  ;
}

export default TaskTimeTrackingButton;
