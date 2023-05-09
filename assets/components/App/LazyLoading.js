import React from 'react';
import InfiniteScroll from "react-infinite-scroller";

const LazyLoading = ({loadMore, hasMore, children}) => {
  const loader = <div className="loader" key={0}>Loading ...</div>
  return (
    <InfiniteScroll pageStart={0} loadMore={loadMore} hasMore={hasMore} loader={loader} initialLoad={false}>
      {children}
    </InfiniteScroll>
  )
}

export default LazyLoading;
