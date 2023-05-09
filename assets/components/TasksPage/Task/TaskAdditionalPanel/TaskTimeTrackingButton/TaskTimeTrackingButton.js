import React from 'react';
import Button from "../../../../App/Button";
import OpenIcon from "../../../../App/OpenIcon";

const TaskTimeTrackingButton = ({task, isActive, events}) => {
  let onFinish = () => events.finishTask(task.id);
  let onStart = () => events.startTask(task.id);
  if (isActive) {
    return (
      <Button onClick={onFinish} buttonStyle='info' tooltip="Finish tracking time">
        <OpenIcon name="media-pause"/>
      </Button>
    )
  }
  return (
    <Button onClick={onStart} tooltip="Start tracking time">
      <OpenIcon name="media-play"/>
    </Button>
  )
}

export default TaskTimeTrackingButton;
