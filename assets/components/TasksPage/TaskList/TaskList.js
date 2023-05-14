import React from 'react';
import Task from "../Task/Task";

const TaskList = ({data, events}) => {
  if (!data.tasks) {
    return null;
  }
  return (
    <div className="tasks">
      {data.tasks.map(task => {
        return <Task key={task.id} task={task} data={data} events={events}/>
      })}
    </div>
  );
}

export default TaskList;
